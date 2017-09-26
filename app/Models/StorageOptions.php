<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 08/09/17
 * Time: 18:26
 */

namespace Equinox\Models;

/**
 * Class StorageOptions
 * @package Equinox\Models
 * @property string $storageType
 */
class StorageOptions extends NonPersistentModel
{

    /**
     * The storage type
     * @var string
     */
    protected $_storageType;

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [];

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'storageType',
    ];

    /**
     * StorageOptions constructor.
     * @param string $storageType
     */
    public function __construct(string $storageType)
    {
        $this->_storageType = $storageType;
    }

}