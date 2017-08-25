<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
$curl->setOpt(CURLOPT_NOBODY, true);
$curl->setOpt(CURLOPT_HEADER, true);
$curl->setUrl('https://httpbin.org/get');
$curl->exec();

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
