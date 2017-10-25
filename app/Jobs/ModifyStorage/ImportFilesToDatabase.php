<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 24/10/17
 * Time: 12:57
 */

namespace Equinox\Jobs\ModifyStorage;


use Equinox\Definitions\Queue;
use Equinox\Jobs\DefaultJob;
use Equinox\Models\QueuePayloadTypes\RQueuePayload;
use Equinox\Services\General\Utils;
use Equinox\Services\Repositories\DataService;
use Equinox\Services\Repositories\QueueService;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class ImportFilesToDatabase
 * @package Equinox\Jobs\ModifyStorage
 */
class ImportFilesToDatabase extends DefaultJob
{

    /**
     * The queue service
     * @var QueueService
     */
    protected $queueService;

    /**
     * The data service
     * @var DataService
     */
    protected $dataService;

    /**
     * ImportFilesToDatabase constructor.
     */
    public function __construct()
    {


    }

    /**
     * Job runner
     * @param DataService $dataService
     * @param QueueService $queueService
     */
    public function handle(
        DataService $dataService,
        QueueService $queueService
    ) {
        Utils::dumpMemUsage();
        $startTime = Utils::startTimer();

        /* Initialize variables */
        $this->dataService = $dataService;
        $this->queueService = $queueService;

        /* Get data from queue */
        $this->listenToQueue();

        Utils::dumpMemUsage();
        $elapsed = Utils::endTimer($startTime);

        Utils::dumpMessage("Processed records in {$elapsed} seconds");
    }

    /**
     * Function used to listen to queue and call callback whenever a message is passed in the queue
     * @return ImportFilesToDatabase
     */
    protected function listenToQueue(): self
    {
        $this->queueService->blockAndListenToQueue(
            Queue::R_QUEUE,
            $this->createCallbackClosure()
        );

        return $this;
    }

    /**
     * Function used to create a queue callback
     * @return \Closure
     */
    protected function createCallbackClosure(): \Closure
    {
        return \Closure::fromCallable([$this, 'onQueueEventTrigger']);
    }

    /**
     * The callback used whenever a queue message is received
     * @param AMQPMessage $AMQPMessage
     */
    public function onQueueEventTrigger(AMQPMessage $AMQPMessage)
    {
        /** @var RQueuePayload $r_QueuePayload */
        $r_QueuePayload = Queue::deserializeQueuePayload($AMQPMessage);

        /* Create temporary storage */

        /* Insert data from CSV file to temporary storage */

        /* Move data from temporary storage to persistent storage */

        /* Delete */

    }


}