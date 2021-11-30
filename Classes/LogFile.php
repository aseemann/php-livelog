<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog;


class LogFile
{
    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int Last line read from logfile
     */
    private $lastReadLine = 0;

    /**
     * LogFile constructor.
     *
     * @param Cookie $cookie
     */
    public function __construct(Cookie $cookie)
    {
        $this->cookie     = $cookie;
        $this->identifier = $this->cookie->getCookie();
    }

    /**
     * Returns the file path of the logFile
     *
     * @return string
     */
    private function getLogFilePath(): string
    {
        return sprintf(Constants::LOG_FILE_PATH_PATTERN, $this->identifier);
    }

    /**
     * Reads log lines from file starting from the given line.
     *
     * @param int $startLine Line to get the logs from.
     *
     * @return array
     */
    public function readLogLines(int $startLine = 0): array
    {
        if (false === file_exists($this->getLogFilePath())) {
            return [];
        }

        $lines = array_slice(file($this->getLogFilePath()), $startLine, 1, true);

        if (empty($lines)) {
            return [];
        }
        end($lines);
        $this->lastReadLine = (int) key($lines) + 1;

        return $lines;
    }

    /**
     * Write a line into the logfile
     *
     * @param LogLine $logLine
     *
     * @return void
     */
    public function writeLogLine(LogLine $logLine): void
    {
        file_put_contents($this->getLogFilePath(), $logLine->getLine(). PHP_EOL, FILE_APPEND);
    }

    /**
     * Returns the last line read from the logfile
     *
     * @return int
     */
    public function getLastReadLine(): int
    {
        return $this->lastReadLine;
    }
}
