<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/10/17
 * Time: 17:40
 */

namespace Equinox\Exceptions;


class DataException extends DefaultException
{

    const INVALID_OPERATION_RECEIVED = 'Invalid operation received';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}