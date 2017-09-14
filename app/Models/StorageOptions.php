<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 08/09/17
 * Time: 18:26
 */

namespace Equinox\Models;

use Equinox\Exceptions\StorageOptionsException;

/**
 * Class StorageOptions
 * @package Equinox\Models
 * @property bool $columnsFlag
 * @property bool $triggersFlag
 * @property string $storageType
 */
class StorageOptions
{
    /**
     * The storage type
     * @var string
     */
    protected $_storageType;

    /**
     * Flag indicating whether storage columns should be generated
     * @var bool
     */
    protected $_columnsFlag;

    /**
     * Flag indicating whether storage triggers should be generated
     * @var bool
     */
    protected $_triggersFlag;

    /**
     * StorageOptions constructor.
     * @param string $storageType
     * @param bool $columnsFlag
     * @param bool $triggersFlag
     */
    public function __construct(string $storageType, bool $columnsFlag, bool $triggersFlag)
    {
        $this->_storageType = $storageType;
        $this->_columnsFlag = $columnsFlag;
        $this->_triggersFlag = $triggersFlag;
    }

    /**
     * Magic method used to return flag option
     * @param string $option
     * @return mixed
     * @throws StorageOptionsException
     */
    public function __get(string $option)
    {
        if (!property_exists($this, "_{$option}")) {
            throw new StorageOptionsException(StorageOptionsException::OPTION_NOT_FOUND);
        }

        return $this->{"_{$option}"};
    }

}