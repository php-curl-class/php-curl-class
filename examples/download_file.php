<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->download('https://php.net/images/logos/php-med-trans.png', '/tmp/php-med-trans.png');
