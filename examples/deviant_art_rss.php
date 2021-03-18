<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->get('http://backend.deviantart.com/rss.xml', [
    'q' => 'boost:popular in:photography/people/fashion',
    'type' => 'deviation',
]);

foreach ($curl->response->channel->item as $entry) {
    $thumbnails = $entry->children('http://search.yahoo.com/mrss/')->thumbnail;
    foreach ($thumbnails as $thumbnail) {
        $img = $thumbnail->attributes();
        echo
            '<a href="' . $entry->link . '">' .
                '<img alt="" src="' . $img->url . '" height="' . $img->height . '" width="' . $img->width . '" />' .
            '</a>';
    }
}
