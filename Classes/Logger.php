<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    public static $REQUEST_ID;

    private $application;

    private $name;

    private $cookie;

    private $logFile;

    public function __construct(string $application, string $name)
    {
        $this->application = $application;
        $this->name        = $name;
        $this->cookie      = new Cookie();
        $this->logFile     = new LogFile($this->cookie);
    }

    public static function getRequestId()
    {
        if (empty(static::$REQUEST_ID)) {
            static::$REQUEST_ID = uniqid();
        }

        return static::$REQUEST_ID;
    }


    public function emergency(string $message, array $context = [])
    {
        $this->log(
            LogLevel::EMERGENCY,
            $message,
            $context
        );
    }

    public function alert(string $message, array $context = [])
    {
        $this->log(
            LogLevel::ALERT,
            $message,
            $context
        );
    }

    public function critical(string $message, array $context = [])
    {
        $this->log(
            LogLevel::CRITICAL,
            $message,
            $context
        );
    }

    public function error(string $message, array $context = [])
    {
        $this->log(
            LogLevel::ERROR,
            $message,
            $context
        );
    }

    public function warning(string $message, array $context = [])
    {
        $this->log(
            LogLevel::WARNING,
            $message,
            $context
        );
    }

    public function notice(string $message, array $context = [])
    {
        $this->log(
            LogLevel::NOTICE,
            $message,
            $context
        );
    }

    public function info(string $message, array $context = [])
    {
        $this->log(
            LogLevel::INFO,
            $message,
            $context
        );
    }

    public function debug(string $message, array $context = [])
    {
        $this->log(
            LogLevel::DEBUG,
            $message,
            $context
        );
    }

    public function log($level, string $message, array $context = [])
    {
        if (false === $this->cookie->hasCookie()) {
            return;
        }

        $logLine = new LogLine(
            $this->application,
            $this->name,
            static::getRequestId(),
            strip_tags($_SERVER['REQUEST_URI']),
            $level,
            $message,
            $context
        );

        $this->logFile->writeLogLine($logLine);
    }
}