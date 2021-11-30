<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog;


class LogLine
{
    /**
     * @var string name of the application
     */
    private $application;

    /**
     * @var string name of the logger
     */
    private $logger;

    /**
     * @var string request id
     */
    private $requestId;

    /**
     * @var string uri of the request
     */
    private $requestUri;

    /**
     * @var string Severity
     */
    private $severity;

    /**
     * @var string log message
     */
    private $message;

    /**
     * @var array context data
     */
    private $context;

    /**
     * @param string $application
     * @param string $logger
     * @param string $requestId
     * @param string $requestUri
     * @param string $severity
     * @param string $message
     * @param array  $context
     */
    public function __construct(
        string $application,
        string $logger,
        string $requestId,
        string $requestUri,
        string $severity,
        string $message,
        array $context = []
    ) {
        $this->application = $application;
        $this->logger      = $logger;
        $this->requestId   = $requestId;
        $this->requestUri  = $requestUri;
        $this->severity    = $severity;
        $this->message     = $message;
        $this->context     = $context;
    }

    /**
     * Returns a json encoded log line
     *
     * @return string
     */
    public function getLine(): string
    {
        return \json_encode(
            [
                'application'   => $this->application,
                'logger'        => $this->logger,
                'requestId'     => $this->requestId,
                'requestUri'    => $this->requestUri,
                'severity'      => $this->severity,
                'message'       => $this->message,
                'context'       => $this->context,
            ]
        );
    }
}
