<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog;

/**
 * Class Constants
 *
 * Collection of constants
 *
 * @package ASeemann\PhpLiveLog
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */
class Constants
{
    /**
     * @var string path pattern for the statefile
     */
    public const STATE_FILE_PATH_PATTERN = '/tmp/phpLiveLog-%s.state';

    /**
     * @var string path pattern for the log-file
     */
    public const LOG_FILE_PATH_PATTERN = '/tmp/phpLiveLog-%s.log';

    /**
     * @var int time in seconds the request will wait for incoming new logs
     */
    public const READ_TIMEOUT = 60;
}
