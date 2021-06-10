<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();
$multi_curl->success(function ($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
});
$multi_curl->error(function ($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});
$multi_curl->complete(function ($instance) {
    echo 'call to "' . $instance->url . '" completed.' . "\n";
});

$multi_curl->addDownload('https://www.php.net/images/logos/php-med-trans.png', '/tmp/php-med-trans.png');
$multi_curl->addDownload('https://upload.wikimedia.org/wikipedia/commons/c/c1/PHP_Logo.png', '/tmp/PHP_Logo.png');
$multi_curl->start();
