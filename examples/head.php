<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl --head "http://127.0.0.1:8000/?key=value"

$curl = new Curl();
$curl->head('http://127.0.0.1:8000/', [
    'key' => 'value',
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Response headers:' . "\n";
    var_dump($curl->responseHeaders);
}
