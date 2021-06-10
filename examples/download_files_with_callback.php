<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$callback = function ($instance, $tmpfile) {
    $save_to_path = '/tmp/' . basename($instance->url);
    $fh = fopen($save_to_path, 'wb');
    stream_copy_to_stream($tmpfile, $fh);
    fclose($fh);
};

$curl = new Curl();
$curl->download('https://www.php.net/images/logos/php-med-trans.png', $callback);
$curl->download('https://upload.wikimedia.org/wikipedia/commons/c/c1/PHP_Logo.png', $callback);
