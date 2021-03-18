<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const INSTAGRAM_CLIENT_ID = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$curl = new Curl();
$curl->get('https://api.instagram.com/v1/media/search', [
    'client_id' => INSTAGRAM_CLIENT_ID,
    'lat' => '37.8296',
    'lng' => '-122.4832',
]);

foreach ($curl->response->data as $media) {
    $image = $media->images->low_resolution;
    echo '<img alt="" src="' . $image->url . '" width="' . $image->width . '" height="' . $image->height . '" />';
}
