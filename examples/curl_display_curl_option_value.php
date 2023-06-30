<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$option = 41; // CURLOPT_VERBOSE = int(41).
$value = true;

$curl = new Curl();
$curl->displayCurlOptionValue($option, $value); // "CURLOPT_VERBOSE: true".
