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

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}