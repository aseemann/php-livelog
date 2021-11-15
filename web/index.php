<?php

namespace ASeemann\PhpLiveLog;


ini_set('display_errors', 'off');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

$garbageCollector = new GarbageCollector();
$garbageCollector->removeOutdatedFiles();

$_COOKIE[Cookie::COOKIE_NAME] = null;

$cookie     = new Cookie();

$replacements = [
    '{{identifier}}' => $cookie->getCookie()
];

echo str_replace(array_keys($replacements), $replacements, file_get_contents('template.html'));
