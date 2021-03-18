<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const YOUTUBE_API_KEY = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$playlistId = 'RDHJb0VYVtaNc';

$curl = new Curl();
$curl->get('https://www.googleapis.com/youtube/v3/playlistItems', [
    'key' => YOUTUBE_API_KEY,
    'maxResults' => '50',
    'part' => 'snippet',
    'playlistId' => $playlistId,
]);

echo 'Songs in this playlist:' . "\n";

foreach ($curl->response->items as $item) {
    echo
        $item->snippet->title . "\n" .
        'https://www.youtube.com/watch?v=' . $item->snippet->resourceId->videoId . "\n" .
        "\n";
}
