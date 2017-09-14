<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:43
 */

namespace Equinox\Exceptions;


class FactoryException extends DefaultException
{

    const INVALID_COLUMN_TYPE_RECEIVED = 'Invalid column type received';
    const INVALID_TABLE_TYPE_RECEIVED = 'Invalid table type received';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        // TODO: Implement report() method.
    }
}