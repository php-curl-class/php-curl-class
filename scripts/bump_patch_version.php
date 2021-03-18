#!/usr/bin/php
<?php
require dirname(__FILE__) . '/../src/Curl/Curl.php';

$current_version = Curl\Curl::VERSION;
list($major, $minor, $patch) = explode('.', $current_version);
$new_version = implode('.', [$major, $minor, (string)((int)$patch += 1)]);

foreach ([
        [
            dirname(__FILE__) . '/../src/Curl/Curl.php',
            '/const VERSION = \'(?:\d+.\d+.\d+)\';/',
            'const VERSION = \'' . $new_version . '\';',
        ],
    ] as $info) {
    list($filepath, $find, $replace) = $info;
    $data = preg_replace($find, $replace, file_get_contents($filepath));
    file_put_contents($filepath, $data);
}

$rightwards_arrow = json_decode('"\u2192"');
echo 'Bump version: ' . $current_version . ' ' . $rightwards_arrow . ' ' . $new_version . "\n";
