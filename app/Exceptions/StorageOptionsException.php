<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 11:45
 */

namespace Equinox\Exceptions;


class StorageOptionsException extends DefaultException
{

    const OPTION_NOT_FOUND = 'Option not found';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}