<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();

for ($i = 0; $i < 10; $i++) {
    for ($j = 0; $j <= 500; $j++) {
        $curl->get('http://127.0.0.1:8000/');
    }
    echo 'memory ' . $i . ': ' . memory_get_usage(true) . "\n";
}
