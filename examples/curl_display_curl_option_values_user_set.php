<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->setUserAgent('some agent');
$curl->setTimeout(60);

foreach ($curl->getUserSetOptions() as $option => $value) {
    echo 'user set option ' . $option . ':' . "\n";
    $curl->displayCurlOptionValue($option, $value);
    echo "\n";
}

// user set option 10018:
// CURLOPT_USERAGENT: "some agent"
//
// user set option 13:
// CURLOPT_TIMEOUT: 60
