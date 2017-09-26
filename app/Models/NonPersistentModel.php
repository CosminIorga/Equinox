<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 18/09/17
 * Time: 12:48
 */

namespace Equinox\Models;


use Equinox\Exceptions\ModelException;

abstract class NonPersistentModel
{

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [];

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [];


    /**
     * Dynamically modify allowed class properties
     * @param string $property
     * @param $value
     * @throws ModelException
     */
    public function __set(string $property, $value)
    {
        /* Throw error if property not allowed to be set */
        if (!in_array($property, $this->settable)) {
            throw new ModelException(ModelException::PROPERTY_NOT_SETTABLE);
        }

        $this->{"_{$property}"} = $value;
    }

    /**
     * Dynamically fetch allowed class properties
     * @param string $property
     * @return mixed
     * @throws ModelException
     */
    public function __get(string $property)
    {
        /* Throw error if property not allowed to be fetched */
        if (!in_array($property, $this->gettable)) {
            throw new ModelException(ModelException::PROPERTY_NOT_GETTABLE);
        }

        return $this->{"_{$property}"};
    }
}