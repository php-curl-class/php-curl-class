<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\MultiCurl;

$server_count = 10;
$urls = array();
$port = 8000;
for ($i = 0; $i < $server_count; $i++) {
    $port += 1;
    $urls[] = 'http://localhost:' . $port . '/';
}

$multi_curl = new MultiCurl();

$success = 0;
$error = 0;
$complete = 0;

$multi_curl->success(function ($instance) use (&$success) {
    $success += 1;
});
$multi_curl->error(function ($instance) use (&$error) {
    $error += 1;
});
$multi_curl->complete(function ($instance) use (&$complete) {
    $complete += 1;
});

$limit = 1000;
for ($i = 0; $i < $limit; $i++) {
    $url = $urls[mt_rand(0, count($urls) - 1)];
    $multi_curl->addGet($url);
}

$multi_curl->start();

echo 'complete: ' . $complete . "\n";
echo 'success: ' . $success . "\n";
echo 'error: ' . $error . "\n";
echo 'done' . "\n";
