<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->verbose();

$curl->displayCurlOptionValue(CURLOPT_VERBOSE);
// "CURLOPT_VERBOSE: true".

$curl->displayCurlOptionValue(41);
// "CURLOPT_VERBOSE: true".

$curl->displayCurlOptionValue(CURLOPT_PROTOCOLS);
// "CURLOPT_PROTOCOLS: 3 (CURLPROTO_HTTP | CURLPROTO_HTTPS)".
