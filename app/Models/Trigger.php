<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 18/09/17
 * Time: 11:37
 */

namespace Equinox\Models;


/**
 * Class Trigger
 * @package Equinox\Models
 * @property string $storageName
 * @property string $triggerName
 * @property string $triggerType
 * @property string $triggerDefinition
 */
class Trigger extends NonPersistentModel
{
    /**
     * The associated
     * @var string
     */
    protected $_storageName;

    /**
     * The trigger name
     * @var string
     */
    protected $_triggerName;
    /**
     * The trigger type
     * @var string
     */
    protected $_triggerType;
    /**
     * The trigger definition
     * @var string
     */
    protected $_triggerDefinition;

    /**
     * Array used to decide which class properties can be set
     * @var array
     */
    protected $settable = [
        'storageName',
        'triggerName',
        'triggerType',
        'triggerDefinition',
    ];

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        'storageName',
        'triggerName',
        'triggerType',
        'triggerDefinition',
    ];

    /**
     * Trigger constructor.
     * @param string $storageName
     * @param string $triggerName
     * @param string $triggerType
     * @param string $triggerDefinition
     */
    public function __construct(
        string $storageName,
        string $triggerName,
        string $triggerType,
        string $triggerDefinition
    ) {
        $this->_storageName = $storageName;
        $this->_triggerName = $triggerName;
        $this->_triggerType = $triggerType;
        $this->_triggerDefinition = $triggerDefinition;
    }

}