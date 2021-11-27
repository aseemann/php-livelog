<?php


namespace ASeemann\PhpLiveLog\Connector\TYPO3;

use ASeemann\PhpLiveLog\Logger;
use TYPO3\CMS\Core\Log\Exception\InvalidLogWriterConfigurationException;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;


class LogWriter extends AbstractWriter implements WriterInterface
{
    private const LEVELS = [
        LogLevel::DEBUG => \Psr\Log\LogLevel::DEBUG,
        LogLevel::INFO => \Psr\Log\LogLevel::INFO,
        LogLevel::NOTICE => \Psr\Log\LogLevel::NOTICE,
        LogLevel::WARNING => \Psr\Log\LogLevel::WARNING,
        LogLevel::ERROR => \Psr\Log\LogLevel::ERROR,
        LogLevel::CRITICAL => \Psr\Log\LogLevel::CRITICAL,
        LogLevel::ALERT => \Psr\Log\LogLevel::ALERT,
        LogLevel::EMERGENCY => \Psr\Log\LogLevel::EMERGENCY,
    ];

    /**
     * @var string
     */
    private $ignorePattern = "";

    /**
     * LogWriter constructor.
     *
     * @param array $options
     *
     * @throws InvalidLogWriterConfigurationException
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    public function setIgnorePattern(string $pattern): LogWriter
    {
        $this->ignorePattern = $pattern;

        return $this;
    }

    /**
     * Returns true if log should be sent
     *
     * @param string $loggerName Name of logger
     *
     * @return bool
     */
    private function shouldSendMessage(string $loggerName): bool
    {
        if (empty($this->ignorePattern)) {
            return true;
        }

        return !preg_match('/' . $this->ignorePattern . '/' , $loggerName);
    }

    /**
     * @param LogRecord $record
     *
     * @return LogWriter
     */
    public function writeLog(LogRecord $record): LogWriter
    {
        if (false === $this->shouldSendMessage($record->getComponent())) {
            return $this;
        }

        $logger = new Logger('TYPO3', $record->getComponent());
        $logger->log(
            self::LEVELS[$record->getLevel()],
            $record->getMessage(),
            ['data' => $record->getData()]
        );

        return $this;
    }
}
