<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 25/09/17
 * Time: 11:33
 */

namespace Equinox\Models;


use Carbon\Carbon;
use Equinox\Exceptions\ModelException;
use Illuminate\Support\Collection;

/**
 * Class NamedStorage
 * @package Equinox\Models
 * @property Collection $columns
 * @property Collection $triggers
 */
class NamedStorage extends NonPersistentModel
{

    /**
     * The storage model
     * @var Storage
     */
    protected $_storage;

    /**
     * The storage triggers
     * @var Collection
     */
    protected $_triggers;

    /**
     * The reference date
     * @var Carbon
     */
    protected $_referenceDate;

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [
        'referenceDate',
        'triggers'
    ];

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'referenceDate',
        'columns',
        'triggers',
    ];

    /**
     * NamedStorage constructor.
     * @param Storage $storage
     * @param Carbon $referenceDate
     */
    public function __construct(Storage $storage, Carbon $referenceDate)
    {
        $this->_storage = $storage;
        $this->_referenceDate = $referenceDate;
    }

    /**
     * Function used to retrieve the storage name
     * @return string
     */
    public function getStorageName(): string
    {
        return $this->_storage->getStorageName($this->_referenceDate);
    }

    /**
     * Function used to retrieve the underlying storage properties
     * @param string $property
     * @return mixed
     * @throws ModelException
     */
    public function __get(string $property)
    {
        switch ($property) {
            case "referenceDate":
                return $this->_referenceDate;
            case "triggers":
                return $this->_triggers;
            case "columns":
                return $this->_storage->columns;
            default:
                throw new ModelException(ModelException::PROPERTY_NOT_GETTABLE);
        }
    }

    /**
     * Function used to check if reference date can be processed by storage.
     * Probably all storage(s) except for Dynamic storage, will return true
     * @return bool
     */
    public function storageShouldHandleCurrentDate(): bool
    {
        return $this->_storage->storageShouldHandleThisDate($this->_referenceDate);
    }

    /**
     * Function used to retrieve the column name by reference date
     * @return string
     */
    public function getIntervalColumnNameByReferenceDate(): string
    {
        return $this->_storage->getIntervalColumnNameByReferenceDate($this->_referenceDate);
    }
}