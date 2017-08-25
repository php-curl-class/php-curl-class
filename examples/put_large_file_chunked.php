<?php
// PUT a file using chunked data.
// See also "examples/receive_large_file_chunked.php".

require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

function read_file($ch, $fd, $length) {
    $data = fread($fd, $length);
    return $data;
}

$filename = 'large_image.png';
$fp = fopen($filename, 'rb');

$curl = new Curl();
$curl->setHeader('Transfer-Encoding', 'chunked');
$curl->setOpt(CURLOPT_UPLOAD, true);
$curl->setOpt(CURLOPT_INFILE, $fp);
$curl->setOpt(CURLOPT_INFILESIZE, filesize($filename));
$curl->setOpt(CURLOPT_READFUNCTION, 'read_file');
$curl->put('http://127.0.0.1:8000/');

fclose($fp);

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
} else {
    echo 'Success' . "\n";
}

// @codingStandardsIgnoreFile
