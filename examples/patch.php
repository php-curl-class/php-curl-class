<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl --request PATCH "https://httpbin.org/patch" --data "a=1&b=2&c=3"

$curl = new Curl();
$curl->patch('https://httpbin.org/patch', [
    'a' => '1',
    'b' => '2',
    'c' => '3',
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
