<?php

namespace ASeemann\PhpLiveLog;

/**
 * Class StateFile
 *
 * @package ASeemann\PhpLiveLog
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */
class StateFile
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
     * StateFile constructor.
     *
     * @param Cookie $cookie
     */
    public function __construct(Cookie $cookie)
    {
        $this->cookie     = $cookie;
        $this->identifier = $this->cookie->getCookie();
    }

    /**
     * Returns the file path of the stateFile
     *
     * @return string
     */
    private function getStateFilePath(): string
    {
        return sprintf(Constants::STATE_FILE_PATH_PATTERN, $this->identifier);
    }

    /**
     * Returns the line form the statefile
     *
     * @return int
     */
    public function getLine(): int
    {
        if (false === file_exists($this->getStateFilePath())) {
            return 0;
        }

        return (int) file_get_contents($this->getStateFilePath());
    }

    /**
     * Set the line number to the stateFile.
     *
     * @param int $line
     */
    public function setLine(int $line): void
    {
        file_put_contents($this->getStateFilePath(), $line);
    }
}
