<?php

namespace ASeemann\PhpLiveLog;

/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

require_once '../vendor/autoload.php';

$cookie     = new Cookie();

$replacements = [
    '{{identifier}}' => $cookie->getCookie()
];

echo str_replace(array_keys($replacements), $replacements, file_get_contents('template.html'));



//$content = file_get_contents(__DIR__ . '/index.html');
//
//$identifier = UUID::uuid5(UUID::NAMESPACE_DNS, 'logbook');
//
//setcookie('logbook', $identifier, 0, '/', $_SERVER['HTTP_HOST']);
//$file = "/tmp/logbook-" . $_COOKIE['logbook'] . ".log";
//$stateFile = "/tmp/logbook-" . $_COOKIE['logbook'] . ".state";
//shell_exec('find /tmp -type f -name "logbook*.log" -mtime +1 --delete');
//shell_exec('echo "" > ' . $file);
//shell_exec('echo "0" > ' . $stateFile = "/tmp/logbook-" . $_COOKIE['logbook'] . ".state");
//
//$replacements = [
//    '{{.Identifier}}' => $identifier,
//];
//
//echo str_replace(array_keys($replacements), $replacements, $content);
