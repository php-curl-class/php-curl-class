<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$data = [];
if (isset($_GET['after'])) {
    $data['after'] = $_GET['after'];
}

$curl = new Curl();
$curl->get('https://www.reddit.com/r/pics/top/.json', $data);

echo '<ul>';

foreach ($curl->response->data->children as $result) {
    $pic = $result->data;
    echo
        '<li>' .
            '<a href="' . $pic->url . '" target="_blank">' .
                $pic->title . '<br />' .
                '<img alt="" src="' . $pic->thumbnail . '" />' .
            '</a> ' .
            $pic->score . ' pts ' . $pic->num_comments . ' comments by ' . $pic->author .
        '</li>';
}

echo '</ul>';
echo '<a href="?after=' . $curl->response->data->after . '">Next</a>';
