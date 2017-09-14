<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/09/17
 * Time: 14:09
 */

namespace Equinox\Services\General;

use Equinox\Definitions\Logger as LoggerHelper;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;

/**
 * Class Logger
 * @package Equinox\Services\General
 * @method void debug(string $message, array $context = [])
 * @method void info(string $message, array $context = [])
 * @method void notice(string $message, array $context = [])
 * @method void warning(string $message, array $context = [])
 * @method void error(string $message, array $context = [])
 * @method void critical(string $message, array $context = [])
 * @method void alert(string $message, array $context = [])
 * @method void emergency(string $message, array $context = [])
 */
class Logger
{
    /**
     * The logger levels
     */
    protected const LEVELS = [
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
     * The Log channels.
     * @var array
     */
    protected $channels = [];

    /**
     * The current channel name
     * @var string
     */
    protected $currentChannel;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->channels = config('logger');
    }

    /**
     * Short function used to set a channel
     * @param string $channelName
     * @return Logger
     */
    public function setChannel(string $channelName): self
    {
        /* Check if channel exists */
        if (!in_array($channelName, array_keys($this->channels))) {
            throw new \InvalidArgumentException('Invalid channel used.');
        }

        $this->currentChannel = $channelName;

        return $this;
    }

    /**
     * Function used to write message to log
     * @param string $level
     * @param string $message
     * @param array $context
     */
    protected function writeLog(string $level, string $message, array $context = [])
    {
        if (is_null($this->currentChannel)) {
            throw new \InvalidArgumentException('No channel selected');
        }

        /* Create channel instance if not defined */
        if (!isset($this->channels[$this->currentChannel][LoggerHelper::LOGGER_INSTANCE])) {
            /* This parse registering */
            $this->parseRegistering($this->channels[$this->currentChannel], $this->currentChannel);
        }

        /* Write out messages */
        $this->channels[$this->currentChannel][LoggerHelper::LOGGER_INSTANCE]->{$level}($message, $context);
    }

    /**
     * Function used to register various handlers to the logger based on channel configuration
     * @param array $channel
     * @param string $channelName
     */
    protected function parseRegistering(array &$channel, string $channelName)
    {
        $channel[LoggerHelper::LOGGER_INSTANCE] = new MonologLogger($channelName);

        /* Process each of the channel's broadcast mediums */
        $mediums = $channel[LoggerHelper::MEDIUMS];

        if (
            array_key_exists(LoggerHelper::REGISTER_TO_FILE_SYSTEM, $mediums)
            && $mediums[LoggerHelper::REGISTER_TO_FILE_SYSTEM]
        ) {
            $this->registerLoggerToFileSystem(
                $channel[LoggerHelper::LOGGER_INSTANCE],
                $channelName,
                $channel[LoggerHelper::MIN_LOG_LEVEL] ?? LoggerHelper::UNKNOWN_LOG_LEVEL
            );
        }
    }

    /**
     * Function used to register logger to a file system log handler
     * @param MonologLogger $logger
     * @param string $channelName
     * @param string $minLevel
     */
    protected function registerLoggerToFileSystem(MonologLogger $logger, string $channelName, string $minLevel)
    {
        $fileName = storage_path(
            implode(DIRECTORY_SEPARATOR, [
                "logs",
                "{$channelName}.log",
            ])
        );

        $handler = new RotatingFileHandler(
            $fileName,
            0,
            $this->computeLoggerSeverityLevel($minLevel)
        );

        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $logger->pushHandler($handler);
    }

    /**
     * Function used to return the integer equivalent for a severity level
     * @param string $level
     * @return int
     */
    protected function computeLoggerSeverityLevel(string $level): int
    {
        return LoggerHelper::LEVELS[$level] ?? 'debug';
    }

    /**
     * Magic method for calling logging methods
     * @param string $func
     * @param array $params
     */
    public function __call(string $func, array $params)
    {
        if (in_array($func, array_keys(self::LEVELS))) {
            $this->writeLog($func, $params[0], $params[1] ?? []);
        }
    }
}