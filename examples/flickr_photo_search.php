<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

function construct_url($photo, $size = 's', $ext = 'jpg')
{
    $url = 'http://farm' . $photo->farm . '.staticflickr.com/' .  $photo->server . '/' .
        $photo->id . '_' . $photo->secret . '_' . $size . '.' . $ext;

    return $url;
}

define('FLICKR_API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

$data = array(
    'method' => 'flickr.photos.search',
    'api_key' => FLICKR_API_KEY,
    'text' => 'happy',
    'sort' => 'interestingness-desc',
    'safe_search' => '3',
    'format' => 'json',
    'nojsoncallback' => '1',
);

$curl = new Curl();
$curl->get('https://api.flickr.com/services/rest/', $data);

foreach ($curl->response->photos->photo as $photo) {
    echo '<img alt="" src="' . construct_url($photo) . '" height="75" width="75" />';
}
