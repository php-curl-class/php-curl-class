<?php
require __DIR__ . '/../vendor/autoload.php';

// Note: The server needs to send a content-length header for progress to work.

use Curl\Curl;

$curl = new Curl();
$curl->progress(function ($client, $download_size, $downloaded, $upload_size, $uploaded) {
    if ($download_size === 0) {
        return;
    }

    // Display a progress bar: xxx% [=======>                                ]
    $progress_size = 40;
    $fraction_downloaded = $downloaded / $download_size;
    $dots = round($fraction_downloaded * $progress_size);
    printf('%3.0f%% [', $fraction_downloaded * 100);
    $i = 0;
    for (; $i < $dots - 1; $i++) {
        echo '=';
    }
    echo '>';
    for (; $i < $progress_size - 1; $i++) {
        echo ' ';
    }
    echo ']' . "\r";
});
$curl->complete(function ($instance) {
    echo "\n" . 'download complete' . "\n";
});
$curl->download('https://www.php.net/distributions/manual/php_manual_en.html.gz', '/tmp/php_manual_en.html.gz');
