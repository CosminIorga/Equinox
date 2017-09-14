<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 18:14
 */

namespace Equinox\Exceptions;


use Equinox\Definitions\Logger;

class ModelException extends DefaultException
{

    const PROPERTY_NOT_SETTABLE = 'Property not settable';
    const PROPERTY_NOT_GETTABLE = 'Property not gettable';

    /**
     * Function should be implemented by all children exceptions
     */
    public function report()
    {
        $this->logger
            ->setChannel(Logger::STORAGE_CHANNEL)
            ->error($this->getMessage(), $this->getContext());
    }
}