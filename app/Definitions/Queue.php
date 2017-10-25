<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/10/17
 * Time: 17:38
 */

namespace Equinox\Definitions;


use Equinox\Models\QueuePayload;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
    const R_QUEUE = 'r_queue';
    const G_QUEUE = 'g_queue';

    const ALLOWED_QUEUES = [
        self::R_QUEUE,
        self::G_QUEUE
    ];

    /**
     * Function used to serialize a queue payload
     * @param QueuePayload $payload
     * @return AMQPMessage
     */
    public static function serializeQueuePayload(QueuePayload $payload): AMQPMessage
    {
        $serializedPayload = serialize($payload);

        return new AMQPMessage($serializedPayload);
    }

    /**
     * Function used to deserialize a queue payload
     * @param AMQPMessage $message
     * @return QueuePayload
     */
    public static function deserializeQueuePayload(AMQPMessage $message): QueuePayload
    {
        $deserializedPayload = unserialize($message->body);

        return $deserializedPayload;
    }

}