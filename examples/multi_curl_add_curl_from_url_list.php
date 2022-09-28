<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;
use Curl\MultiCurl;

$concurrency = 3;
$urls = [
    'https://www.example.com/?' . md5(mt_rand()),
    'https://www.example.com/?' . md5(mt_rand()),
    'https://www.example.com/?' . md5(mt_rand()),
    'https://www.example.com/?' . md5(mt_rand()),
    'https://www.example.com/?' . md5(mt_rand()),
    // etc.
];

$multi_curl = new MultiCurl();
$multi_curl->setConcurrency($concurrency);
$multi_curl->complete(function ($instance) use (&$multi_curl, &$urls) {
    echo 'complete:' . $instance->url . "\n";

    // Queue another request each time a request completes. Fetch the oldest url
    // next using array_shift($urls) or use the most recently added url using
    // array_pop($urls).
    //   $next_url = array_shift($urls);
    //   $next_url = array_pop($urls);
    $next_url = array_shift($urls);

    if ($next_url !== null) {
        $multi_curl->addGet($next_url);
    }
});

// Queue a few requests.
for ($i = 0; $i < $concurrency; $i++) {
    $next_url = array_shift($urls);
    if ($next_url !== null) {
        $multi_curl->addGet($next_url);
    }
}

$multi_curl->start();
