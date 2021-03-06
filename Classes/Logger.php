<?php

namespace ASeemann\PhpLiveLog;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Logger
 *
 * @package ASeemann\PhpLiveLog
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 *
 */
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
    }

    private function getLogFile(): ?LogFile
    {
        if ($this->logFile instanceof LogFile) {
            return $this->logFile;
        }

        if (false === $this->cookie->hasCookie()) {
            return null;
        }

        $this->logFile = new LogFile($this->cookie);
        return $this->logFile;
    }

    public static function getRequestId()
    {
        if (empty(static::$REQUEST_ID)) {
            static::$REQUEST_ID = uniqid();
        }

        return static::$REQUEST_ID;
    }

    public function emergency($message, array $context = [])
    {
        $this->log(
            LogLevel::EMERGENCY,
            $message,
            $context
        );
    }

    public function alert($message, array $context = [])
    {
        $this->log(
            LogLevel::ALERT,
            $message,
            $context
        );
    }

    public function critical($message, array $context = [])
    {
        $this->log(
            LogLevel::CRITICAL,
            $message,
            $context
        );
    }

    public function error($message, array $context = [])
    {
        $this->log(
            LogLevel::ERROR,
            $message,
            $context
        );
    }

    public function warning($message, array $context = [])
    {
        $this->log(
            LogLevel::WARNING,
            $message,
            $context
        );
    }

    public function notice($message, array $context = [])
    {
        $this->log(
            LogLevel::NOTICE,
            $message,
            $context
        );
    }

    public function info($message, array $context = [])
    {
        $this->log(
            LogLevel::INFO,
            $message,
            $context
        );
    }


    public function debug($message, array $context = [])
    {
        $this->log(
            LogLevel::DEBUG,
            $message,
            $context
        );
    }

    public function log($level, $message, array $context = [])
    {
        if (false === $this->cookie->hasCookie() || false === $this->getLogFile() instanceof LogFile) {
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

        $this->getLogFile()->writeLogLine($logLine);
    }
}
