<?php

// keywords: diagnose, troubleshoot, help

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->get('https://httpbin.org/status/400');

if ($curl->error) {
    echo 'An error occurred:' . "\n";
    $curl->diagnose();
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
