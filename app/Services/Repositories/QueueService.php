<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/10/17
 * Time: 17:35
 */

namespace Equinox\Services\Repositories;


use Equinox\Definitions\Queue;
use Equinox\Exceptions\QueueException;
use Equinox\Models\QueuePayload;
use Illuminate\Support\Collection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class QueueService
{
    /**
     * A collection of opened connections
     * @var Collection
     */
    protected $openConnections;

    /**
     * QueueService constructor.
     */
    public function __construct()
    {
        $this->openConnections = collect([]);
    }

    /**
     * Function used to close queue connection
     */
    public function __destruct()
    {
        foreach ($this->openConnections->keys() as $queueName) {
            $this->closeQueueConnection($queueName);
        }
    }

    /**
     * Function used to open a queue connection
     * @param string $queueName
     * @return bool
     */
    public function openQueueConnection(string $queueName): bool
    {
        $queueConfig = array_values(config('queue.connections.rabbitmq'));

        $connection = new AMQPStreamConnection(...$queueConfig);

        $channel = $connection->channel();
        $channel->queue_declare(
            $queueName,
            false,
            false,
            false,
            false
        );

        $this->setQueueConnection($queueName, $connection);

        return true;
    }

    /**
     * Function used to close a queue connection
     * @param string $queueName
     * @return bool
     */
    public function closeQueueConnection(string $queueName): bool
    {
        $connection = $this->getQueueConnection($queueName);

        $connection->channel()->close();
        $connection->close();

        $this->forgetQueueConnection($queueName);

        return true;
    }

    /**
     * Function used to schedule a payload to a queue
     * @param QueuePayload $payload
     * @param string $queueName
     */
    public function scheduleToQueue(string $queueName, QueuePayload $payload)
    {
        $this->validateQueueName($queueName)
            ->putInQueue($queueName, $payload);
    }

    /**
     * Short function used to validate the given queue name
     * @param string $queueName
     * @return QueueService
     * @throws QueueException
     */
    protected function validateQueueName(string $queueName): self
    {
        if (!in_array($queueName, Queue::ALLOWED_QUEUES)) {
            throw new QueueException(QueueException::UNDEFINED_QUEUE_NAME, [
                'Queue Name' => $queueName,
            ]);
        }

        return $this;
    }

    /**
     * Add message to queue
     * @param string $queueName
     * @param QueuePayload $queuePayload
     * @return QueueService
     */
    protected function putInQueue(string $queueName, QueuePayload $queuePayload): self
    {
        $message = Queue::serializeQueuePayload($queuePayload);

        $connection = $this->getQueueConnection($queueName);

        $connection->channel()->basic_publish($message, '', $queueName);

        return $this;
    }

    /**
     * Function used to listen to given queue and call callback for a set number of times
     * @param string $queueName
     * @param \Closure $callback
     * @param int $callbackCount
     */
    public function blockAndListenToQueue(string $queueName, \Closure $callback, int $callbackCount = null)
    {
        $connection = $this->getQueueConnection($queueName);

        $channel = $connection->channel();

        $channel->basic_consume(
            $queueName,
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        $currentEventsRead = 0;

        while (is_null($callbackCount) || $currentEventsRead < $callbackCount) {
            $channel->wait();

            $currentEventsRead++;
        }
    }

    /**
     * Short function used to retrieve queue connection given queue name
     * @param string $queueName
     * @return AMQPStreamConnection
     * @throws QueueException
     */
    protected function getQueueConnection(string $queueName): AMQPStreamConnection
    {
        $connection = $this->openConnections->get($queueName);

        if (is_null($connection)) {
            throw new QueueException(QueueException::UNDEFINED_QUEUE_NAME, [
                'Queue Name' => $queueName,
            ]);
        }

        return $this->openConnections->get($queueName);
    }

    /**
     * Short function used to set queue connection
     * @param string $queueName
     * @param AMQPStreamConnection $connection
     * @return $this
     */
    protected function setQueueConnection(string $queueName, AMQPStreamConnection $connection)
    {
        $this->openConnections->put($queueName, $connection);

        return $this;
    }

    /**
     * Short function used to remove an open queue connection from the connections pool
     * @param string $queueName
     * @return $this
     */
    protected function forgetQueueConnection(string $queueName)
    {
        $this->openConnections->forget($queueName);

        return $this;
    }
}