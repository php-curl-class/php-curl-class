#!/usr/bin/env php
<?php
require __DIR__ . '/../src/Curl/BaseCurl.php';
require __DIR__ . '/../src/Curl/Curl.php';

$current_version = Curl\Curl::VERSION;
list($major, $minor, $_) = explode('.', $current_version);
$new_version = implode('.', [$major, (string)((int)$minor += 1), '0']);

foreach ([
        [
            __DIR__ . '/../src/Curl/Curl.php',
            '/const VERSION = \'(?:\d+.\d+.\d+)\';/',
            'const VERSION = \'' . $new_version . '\';',
        ],
    ] as $info) {
    list($filepath, $find, $replace) = $info;
    $data = preg_replace($find, $replace, file_get_contents($filepath));
    file_put_contents($filepath, $data);
}

$rightwards_arrow = json_decode('"\u2192"');
$message = 'Bump version: ' . $current_version . ' ' . $rightwards_arrow . ' ' . $new_version;

echo json_encode([
    'message' => $message,
    'old_version' => $current_version,
    'new_version' => $new_version,
], JSON_PRETTY_PRINT) . "\n";
