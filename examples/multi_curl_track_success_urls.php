<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

// List of pages to fetch.
$urls = [
    'https://httpbin.org/status/503',
    'https://httpbin.org/status/200',
    'https://httpbin.org/status/401',
    'https://httpbin.org/delay/3',
    'https://httpbin.org/status/201',
    'https://httpbin.org/delay/1',
    'https://httpbin.org/status/500',
    'https://httpbin.org/status/504',
];

$request_stats = [
    'all' => [],
    'successful' => [],
    'errors' => [],
    'completed' => [],
    'not_completed' => [],
];

$multi_curl = new MultiCurl();
$multi_curl->setConcurrency(2);

foreach ($urls as $url) {
    // Queue requests.
    $request = $multi_curl->addGet($url);

    // Track all requests queued.
    $request_stats['all'][] = $request->url;
}

// Track successful requests.
$multi_curl->success(function ($instance) use (&$request_stats, $multi_curl) {
    $request_stats['successful'][] = $instance->url;

    // Optionally, stop additional requests based on some condition (e.g. stop
    // after a number of successful requests).
    if (count($request_stats['successful']) >= 3) {
        $multi_curl->stop();
    }
});

// Track requests that errored.
$multi_curl->error(function ($instance) use (&$request_stats) {
    $request_stats['errors'][] = $instance->url;
});

// Track requests that completed.
$multi_curl->complete(function ($instance) use (&$request_stats) {
    $request_stats['completed'][] = $instance->url;
});

$multi_curl->start();

// Determine urls not completed.
$request_stats['not_completed'] = array_diff($request_stats['all'], $request_stats['completed']);

// Display results.
var_dump($request_stats);

/*
$ php multi_curl_track_success_urls.php
array(5) {
  ["all"]=>
  array(8) {
    [0]=>
    string(30) "https://httpbin.org/status/503"
    [1]=>
    string(30) "https://httpbin.org/status/200"
    [2]=>
    string(30) "https://httpbin.org/status/401"
    [3]=>
    string(27) "https://httpbin.org/delay/3"
    [4]=>
    string(30) "https://httpbin.org/status/201"
    [5]=>
    string(27) "https://httpbin.org/delay/1"
    [6]=>
    string(30) "https://httpbin.org/status/500"
    [7]=>
    string(30) "https://httpbin.org/status/504"
  }
  ["successful"]=>
  array(3) {
    [0]=>
    string(30) "https://httpbin.org/status/200"
    [1]=>
    string(27) "https://httpbin.org/delay/3"
    [2]=>
    string(30) "https://httpbin.org/status/201"
  }
  ["errors"]=>
  array(2) {
    [0]=>
    string(30) "https://httpbin.org/status/503"
    [1]=>
    string(30) "https://httpbin.org/status/401"
  }
  ["completed"]=>
  array(5) {
    [0]=>
    string(30) "https://httpbin.org/status/200"
    [1]=>
    string(30) "https://httpbin.org/status/503"
    [2]=>
    string(30) "https://httpbin.org/status/401"
    [3]=>
    string(27) "https://httpbin.org/delay/3"
    [4]=>
    string(30) "https://httpbin.org/status/201"
  }
  ["not_completed"]=>
  array(3) {
    [5]=>
    string(27) "https://httpbin.org/delay/1"
    [6]=>
    string(30) "https://httpbin.org/status/500"
    [7]=>
    string(30) "https://httpbin.org/status/504"
  }
}
*/
