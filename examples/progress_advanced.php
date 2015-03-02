<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

$curl = new Curl();
$curl->progress(function($client, $download_size, $downloaded, $upload_size, $uploaded) {
    if ($download_size === 0) {
        return;
    }

    // Display a progress bar: xxx% [=======>             ]
    $percent = (int)floor( $downloaded * 100 / $download_size );
    $percentage = sprintf('%3d%%', $percent);
    $arrow_length = 40;
    $arrow_tail_length = max(1, floor(($percent / 100 * $arrow_length) - 3));
    $space_length = max(0, $arrow_length - $arrow_tail_length - 3);
    $arrow = '[' . str_repeat('=', $arrow_tail_length) . '>' . str_repeat(' ', $space_length) . ']';
    echo ' ' . $percentage . ' ' . $arrow . "\r";
});
$curl->complete(function($instance) {
    echo "\n" . 'download complete' . "\n";
});
$curl->download('https://php.net/distributions/manual/php_manual_en.html.gz', '/tmp/php_manual_en.html.gz');
