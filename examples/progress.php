<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

$last_progress = 0;
$last_progress_time = 0;

$curl = new Curl();
$curl->progress(function ($client, $download_size, $downloaded, $upload_size, $uploaded){

    global $last_progress;
    global $last_progress_time;

    if($download_size == 0)
        return;

    $now = time();

    $progress = floor( $downloaded * 100 / $download_size );

    // print progress every one second
    if(($now - $last_progress_time) > 1 && $last_progress != $progress)
    {
        echo $progress . "%\n";

        $last_progress      = $progress;
        $last_progress_time = $now;
    }
});
$curl->download('http://php.net/distributions/manual/php_manual_en.html.gz', '/tmp/php_manual_en.html.gz');
