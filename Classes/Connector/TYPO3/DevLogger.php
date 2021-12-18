<?php

namespace ASeemann\PhpLiveLog\Connector\TYPO3;

use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DevLogger (TYPO3 DevLogger)
 *
 * @package ASeemann\PhpLiveLog\Connector\TYPO3
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */
class DevLogger
{
    /**
     * @var array Mapping Array for logLevels
     */
    private $severityMap = [
        GeneralUtility::SYSLOG_SEVERITY_FATAL   => LogLevel::EMERGENCY,
        GeneralUtility::SYSLOG_SEVERITY_ERROR   => LogLevel::ERROR,
        GeneralUtility::SYSLOG_SEVERITY_WARNING => LogLevel::WARNING,
        GeneralUtility::SYSLOG_SEVERITY_NOTICE  => LogLevel::NOTICE,
        GeneralUtility::SYSLOG_SEVERITY_INFO    => LogLevel::INFO
    ];

    /**
     * DevLogger
     *
     * @param array $params Array with logging parameters
     *
     * @return void
     */
    public function devLogger(array $params)
    {
        /**
         * @var LogManager $logManager
         */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $logger = $logManager->getLogger($params['extKey']);

        $data = is_array($params['dataVar']) ? $params['dataVar'] : [];

        $severity = $this->translateSeverity($params['severity']);

        $logger->log($severity, $params['msg'], $data);
    }

    /**
     * Translate the loglevel from syslog to rfc
     *
     * @param int $sysLogLevel sysLogLevel
     *
     * @return int
     */
    private function translateSeverity(int $sysLogLevel): int
    {
        if ($sysLogLevel < 0) {
            return LogLevel::DEBUG;
        }

        if (isset($this->severityMap[$sysLogLevel])) {
            return $this->severityMap[$sysLogLevel];
        }

        return 0;
    }
}
