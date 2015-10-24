<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$video_ids = array(
    '9bZkp7q19f0',
    '_OBlgSz8sSM',
    'uelHwf8o7_U',
    'KQ6zr6kCPj8',
    'ASO_zypdnsQ',
    'pRpeEdMmmQ0',
);

foreach ($video_ids as $video_id) {
    $curl = new Curl();
    $curl->get('https://gdata.youtube.com/feeds/api/videos/' . $video_id . '?alt=json');
    echo '"' . $curl->response->entry->title->{'$t'} . '" has ' .
        number_format($curl->response->entry->{'yt$statistics'}->viewCount) . ' views.' . "\n";
}
