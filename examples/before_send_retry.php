<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$max_retries = 3;

$curl = new Curl();
$curl->setRetry($max_retries);

$curl->beforeSend(function ($instance) {
    echo 'current attempts: ' . $instance->attempts . "\n";
    echo 'current retries: ' . $instance->retries . "\n";
    echo 'about to make request to ' . $instance->url . "\n";
});

$curl->get('https://httpbin.org/status/503');

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
    echo 'final attempts: ' . $curl->attempts . "\n";
    echo 'final retries: ' . $curl->retries . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
