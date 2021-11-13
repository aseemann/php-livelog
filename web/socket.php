<?php

namespace ASeemann\PhpLiveLog;

/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

require_once '../vendor/autoload.php';

ini_set('display_errors', 'Off');
session_abort();
header('Content-Type: application/json');

$cookie     = new Cookie();
$logFile    = new LogFile($cookie);
$stateFile  = new StateFile($cookie);

$timeOutTime = time() + Constants::READ_TIMEOUT;

while (time() < $timeOutTime) {

    $entries = $logFile->readLogLines($stateFile->getLine());

    if (false === empty($entries)) {
        $stateFile->setLine($logFile->getLastReadLine());
        die("[" . implode(',', $entries) . "]");
    }
}

die("[]");
