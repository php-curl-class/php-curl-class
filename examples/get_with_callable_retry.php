<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->setRetry(function ($instance) {
    return $instance->retries < 3;
});
$curl->get('https://httpbin.org/status/503');

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
    echo 'attempts: ' . $curl->attempts . "\n";
    echo 'retries: ' . $curl->retries . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
