<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 11:45
 */

namespace Equinox\Exceptions;


class StorageException extends DefaultException
{

    const STORAGE_CANNOT_PROCESS_SUCH_DATE = 'Storage cannot process given reference date';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}