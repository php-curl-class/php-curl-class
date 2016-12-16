<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\MultiCurl;

$callback_success = function ($instance, $tmpfile) {
    $url = $instance->url;
    $save_to_path = '/tmp/' . basename($instance->url);
    $fh = fopen($save_to_path, 'wb');
    stream_copy_to_stream($tmpfile, $fh);
    fclose($fh);
    echo "{$url} stored to {$save_to_path} \n";
};

$callback_error = function ($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
};

$multi_curl = new MultiCurl();
$multi_curl->addDownloadConditional('https://secure.php.net/images/logos/php-med-trans.png', $callback_success, $callback_error);
$multi_curl->addDownloadConditional('http://karel.wintersky.ru/file.txt',  $callback_success, $callback_error); // this file does not exist
$multi_curl->start();
 
