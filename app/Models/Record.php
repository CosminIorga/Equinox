<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 19/09/17
 * Time: 16:59
 */

namespace Equinox\Models;


use Illuminate\Support\Collection;

/**
 * Class Record
 * @package Equinox\Models
 * @property string $storageName
 * @property Collection $values
 */
class Record extends NonPersistentModel
{

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'values',
        'storageName'
    ];

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [];

    /**
     * The storage used to store the record
     * @var string
     */
    protected $_storageName;

    /**
     * An association between a column name and a value
     * @var Collection
     */
    protected $_values;

    /**
     * Record constructor.
     * @param string $storageName
     * @param Collection $values
     */
    public function __construct(string $storageName, Collection $values)
    {
        $this->_storageName = $storageName;
        $this->_values = $values;
    }

    /**
     * Function used to set a value for given column
     * @param string $columnName
     * @param mixed $value
     * @return Record
     */
    public function setValueForColumn(string $columnName, $value): self
    {
        $this->_values->put($columnName, $value);

        return $this;
    }


}