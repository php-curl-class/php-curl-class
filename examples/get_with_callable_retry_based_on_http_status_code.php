<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$max_retries = 3;

$curl = new Curl();
$curl->setRetry(function ($instance) use ($max_retries) {
    // Retry when the result of curl_getinfo($instance->curl, CURLINFO_HTTP_CODE) is 500, 503.
    return $instance->retries < $max_retries && in_array($instance->httpStatusCode, [500, 503]);
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
