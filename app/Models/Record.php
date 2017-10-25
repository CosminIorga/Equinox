<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 19/09/17
 * Time: 16:59
 */

namespace Equinox\Models;


use Equinox\Services\General\Utils;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Class Record
 * @package Equinox\Models
 * @property string $storageName
 * @property Collection $values
 * @property string $hash
 */
class Record extends NonPersistentModel implements Arrayable
{

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'values',
        'storageName',
        'hash',
    ];

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [
        'hash',
        'values',
    ];

    /**
     * The storage used to store the record
     * @var string
     */
    protected $_storageName;

    /**
     * The record hash
     * @var string
     */
    protected $_hash;

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


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'storage' => $this->_storageName,
            'hash' => $this->_hash,
            'values' => $this->_values,
        ];
    }

    /**
     * Short function used to export the record values as array
     * @return array
     */
    public function toCSVData(): array
    {
        return $this->_values->map(function ($value) {
            return (is_null($value)) ? null : Utils::replaceQuotes($value);
        })->toArray();
    }
}