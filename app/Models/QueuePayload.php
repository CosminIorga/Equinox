<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/10/17
 * Time: 17:27
 */

namespace Equinox\Models;


use Equinox\Exceptions\QueueException;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class QueuePayload
 * @package Equinox\Models
 */
abstract class QueuePayload implements Jsonable
{

    /**
     * The payload as an array that should be sent via queue
     * @var array
     */
    protected $payload;

    /**
     * QueuePayload constructor.
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->validatePayload($payload)
            ->save($payload);
    }

    /**
     * Function used to validate the given payload
     * @param array $payload
     * @return QueuePayload
     * @throws QueueException
     */
    protected function validatePayload(array $payload): self
    {
        $rules = $this->getValidationRules();

        $validator = \Validator::make($payload, $rules);

        if ($validator->fails()) {
            throw new QueueException(QueueException::VALIDATION_FAILED, $validator->errors());
        }

        return $this;
    }

    /**
     * Short function used to save the payload
     * @param array $payload
     * @return QueuePayload
     */
    protected function save(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Function used to transform payload to JSON
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->payload);
    }

    /**
     * Function used to return the validation rules for queue payload
     * @return array
     */
    abstract protected function getValidationRules(): array;
}