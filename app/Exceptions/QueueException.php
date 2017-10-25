<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/10/17
 * Time: 17:40
 */

namespace Equinox\Exceptions;


class QueueException extends DefaultException
{

    const UNDEFINED_QUEUE_NAME = 'Undefined queue name';
    const VALIDATION_FAILED = 'Validation failed';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}