<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// Fetch first 50 bytes. Server needs to support the Range header.
$curl = new Curl();
$curl->setRange('0-49');
$curl->get('https://code.jquery.com/jquery-1.11.2.min.js');

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
} else {
    var_dump($curl->responseHeaders['status-line']); // HTTP/1.1 206 Partial Content
    var_dump($curl->responseHeaders['content-length']); // 50
    var_dump($curl->responseHeaders['content-range']); // bytes 0-49/95931
    var_dump($curl->response);
}
