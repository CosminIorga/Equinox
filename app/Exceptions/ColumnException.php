<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 11:45
 */

namespace Equinox\Exceptions;


class ColumnException extends DefaultException
{

    const VALIDATION_FAILED = 'Validation failed.';
    const UNDEFINED_COLUMN = 'Undefined column';
    const INVALID_CONFIG_FUNCTION_RECEIVED = 'Invalid config function received';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}