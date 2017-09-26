<?php

namespace Equinox\Models;


use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Storage
 * @package Equinox\Models
 * @property Collection $columns
 * @property Carbon $referenceDate
 */
abstract class Storage extends NonPersistentModel
{
    /**
     * The pattern name used by interval columns
     */
    protected const INTERVAL_COLUMN_PATTERN_NAME = 'interval_:start_interval:_:end_interval:';

    /**
     * Value used to determine the period stored in each data column
     * @var int
     */
    protected $dataElasticity;

    /**
     * The storage columns
     * @var Collection
     */
    protected $_columns;

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [
        'columns',
    ];

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'columns',
    ];

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        $this->dataElasticity = config('general.data_elasticity');

        $this->_columns = collect([]);
    }

    /**
     * Function used to check if given reference date can be processed by storage.
     * Probably all storage(s) except for Dynamic storage, will return true
     * @param Carbon $referenceDate
     * @return bool
     */
    abstract public function storageShouldHandleThisDate(Carbon $referenceDate): bool;

    /**
     * Function used to return interval column count
     * @return int
     */
    abstract public function getIntervalColumnCount(): int;

    /**
     * Function used to retrieve the storage name
     * @param Carbon $referenceDate
     * @return string
     */
    abstract public function getStorageName(Carbon $referenceDate): string;

    /**
     * Function used to return the interval column name given the interval column index
     * @param int $columnIndex
     * @return string
     */
    abstract public function getIntervalColumnNameByIndex(int $columnIndex): string;

    /**
     * Function used to return the interval column name by reference date
     * @param Carbon $referenceDate
     * @return string
     */
    abstract public function getIntervalColumnNameByReferenceDate(Carbon $referenceDate): string;
}
