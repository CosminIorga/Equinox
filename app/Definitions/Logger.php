<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/09/17
 * Time: 14:39
 */

namespace Equinox\Definitions;

use Monolog\Logger as MonologLogger;

class Logger
{
    const LOGGER_INSTANCE = '_instance';
    const MIN_LOG_LEVEL = 'min_level';
    const MEDIUMS = 'mediums';

    /**
     * Minimum default log level
     */
    const UNKNOWN_LOG_LEVEL = 'info';

    /**
     * Severity levels
     */
    const LEVELS = [
        'debug' => MonologLogger::DEBUG,
        'info' => MonologLogger::INFO,
        'notice' => MonologLogger::NOTICE,
        'warning' => MonologLogger::WARNING,
        'error' => MonologLogger::ERROR,
        'critical' => MonologLogger::CRITICAL,
        'alert' => MonologLogger::ALERT,
        'emergency' => MonologLogger::EMERGENCY,
    ];

    /**
     * Channel mediums
     */
    const REGISTER_TO_FILE_SYSTEM = 'registerToFileSystem';


    /**
     * Available channels
     */
    const DEFAULT_CHANNEL = 'default_channel';
    const STORAGE_CHANNEL = 'storage';
    const MODIFY_DATA_CHANNEL = 'alter_data';
    const FETCH_DATA_CHANNEL = 'fetch_data';
    const GEARMAN_CHANNEL = 'gearman';

}