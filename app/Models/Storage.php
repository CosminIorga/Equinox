<?php

namespace Equinox\Models;


use Carbon\Carbon;
use Equinox\Exceptions\ModelException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Class Storage
 * @package Equinox\Models
 * @property Collection $columns
 * @property Collection $triggers
 * @property Carbon $referenceDate
 */
abstract class Storage implements Arrayable
{

    /**
     * Value used to determine the period stored in each data column
     * @var int
     */
    protected $dataElasticity;

    /**
     * The reference date
     * @var Carbon
     */
    protected $_referenceDate;

    /**
     * The storage columns
     * @var Collection
     */
    protected $_columns;

    /**
     * The storage triggers
     * @var Collection
     */
    protected $_triggers;

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [
        'columns',
        'referenceDate',
        'triggers'
    ];

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'columns',
        'referenceDate',
        'triggers'
    ];

    /**
     * Storage constructor.
     * @param Carbon $referenceDate
     */
    public function __construct(Carbon $referenceDate)
    {
        $this->_referenceDate = $referenceDate;

        $this->dataElasticity = config('general.data_elasticity');

        $this->_columns = collect([]);
        $this->_triggers = collect([]);
    }

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
        if (!in_array($property, $this->settable)) {
            throw new ModelException(ModelException::PROPERTY_NOT_GETTABLE);
        }

        return $this->{"_{$property}"};
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        return [
            'referenceDate' => $this->_referenceDate,
            'dataElasticity' => $this->dataElasticity,
            'columns' => $this->_columns->toArray(),
            'triggers' => $this->_triggers->toArray()
        ];
    }

    /**
     * Function used to return interval column count
     * @return int
     */
    abstract public function getIntervalColumnCount(): int;

    /**
     * Function used to return a value for given coordinate based on table interval
     * @param int $coordinate
     * @return string
     */
    abstract public function getValueForCoordinate(int $coordinate): string;

    /**
     * Function used to retrieve the storage name
     * @return string
     */
    abstract public function getStorageName(): string;

    /**
     * Function used to retrieve the first datetime at which the storage holds information
     * @return Carbon
     */
    abstract public function getStorageStartDate(): Carbon;


}
