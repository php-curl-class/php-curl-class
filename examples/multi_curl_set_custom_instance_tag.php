<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$tags_to_urls = [
    'tag3' => 'https://httpbin.org/status/401',
    'tag4' => 'https://httpbin.org/status/200',
    'tag5' => 'https://httpbin.org/status/503',
];

$ids_to_tags = [];

$multi_curl = new MultiCurl();

$multi_curl->success(function ($instance) use (&$ids_to_tags) {
    echo
        'instance id ' . $instance->id . ' request with tag ' .
        $ids_to_tags[$instance->id] .  ' was successful.' . "\n";
});
$multi_curl->error(function ($instance) use (&$ids_to_tags) {
    echo
        'instance id ' . $instance->id . ' request with tag ' .
        $ids_to_tags[$instance->id] .  ' was unsuccessful.' . "\n";
});
$multi_curl->complete(function ($instance) use (&$ids_to_tags) {
    echo
        'instance id ' . $instance->id . ' request with tag ' .
        $ids_to_tags[$instance->id] . ' completed.' . "\n";
});

foreach ($tags_to_urls as $tag => $url) {
    $curl = $multi_curl->addGet($url, ['myTag' => $tag]);
    $ids_to_tags[$curl->id] = $tag;
}

$multi_curl->start();

/*
$ php multi_curl_set_custom_instance_tag.php
instance id 0 request with tag tag3 was unsuccessful.
instance id 0 request with tag tag3 completed.
instance id 2 request with tag tag5 was unsuccessful.
instance id 2 request with tag tag5 completed.
instance id 1 request with tag tag4 was unsuccessful.
instance id 1 request with tag tag4 completed.
*/
