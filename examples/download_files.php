<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->download('https://www.php.net/images/logos/php-med-trans.png', '/tmp/php-med-trans.png');
$curl->download('https://upload.wikimedia.org/wikipedia/commons/c/c1/PHP_Logo.png', '/tmp/PHP_Logo.png');
