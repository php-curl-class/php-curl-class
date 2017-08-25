<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\MultiCurl;

$multi_curl = new MultiCurl();

for ($i = 0; $i < 10; $i++) {
    for ($j = 0; $j <= 500; $j++) {
        $multi_curl->addGet('http://127.0.0.1:8000/');
    }
    $multi_curl->start();
    echo 'memory ' . $i . ': ' . memory_get_usage(true) . "\n";
}
