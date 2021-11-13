<?php

namespace ASeemann\PhpLiveLog;

ini_set('display_errors', 'off');

/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

if (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
}

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
