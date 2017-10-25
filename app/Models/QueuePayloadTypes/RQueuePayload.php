<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/10/17
 * Time: 17:28
 */

namespace Equinox\Models\QueuePayloadTypes;


use Equinox\Models\NamedStorage;
use Equinox\Models\QueuePayload;

class RQueuePayload extends QueuePayload
{

    const FILE_NAME = 'file_name';
    const NAMED_STORAGE = 'named_storage';

    /**
     * Function used to return the validation rules for queue payload
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            self::FILE_NAME => 'required|string',
            self::NAMED_STORAGE => 'required',
        ];
    }

    /**
     * Get the named storage from payload
     * @return NamedStorage
     */
    public function getNamedStorage(): NamedStorage
    {
        return $this->payload[self::NAMED_STORAGE];
    }

    /**
     * Get the CSV file name from payload
     * @return string
     */
    public function getCSVFIleName(): string
    {
        return $this->payload[self::FILE_NAME];
    }
}