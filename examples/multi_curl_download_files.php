<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();
$multi_curl->addDownload('https://www.php.net/images/logos/php-med-trans.png', '/tmp/php-med-trans.png');
$multi_curl->addDownload('https://upload.wikimedia.org/wikipedia/commons/c/c1/PHP_Logo.png', '/tmp/PHP_Logo.png');
$multi_curl->start();
