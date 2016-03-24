<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->download('https://secure.php.net/images/logos/php-med-trans.png', function($instance, $tmpfile) {
    $save_to_path = '/tmp/' . basename($instance->url);
    $fh = fopen($save_to_path, 'wb');
    stream_copy_to_stream($tmpfile, $fh);
    fclose($fh);
});
