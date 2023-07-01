<?php

require __DIR__ . '/../vendor/autoload.php';

// Usage:
//   1. In separate windows, run one or both of the following files to start the download progress watchers:
//
//      $ ipython multi_curl_progress_advanced_watch_tqdm.py
//      $ ipython multi_curl_progress_advanced_watch_curses.py
//
//   2. In a separate window, run the current file to download some files and emit progress updates.
//
//      $ php multi_curl_progress_advanced.php
//
//   3. Download progress is shown:
//
//      $ ipython multi_curl_progress_advanced_watch_tqdm.py
//      php_manual_en.html.gz:  56%|████████████████████████                   | 2.99M/5.34M [00:02<00:02, 1.14MB/s]
//      php_manual_en.tar.gz:  23%|██████████▎                                  | 2.37M/10.4M [00:02<00:08, 901kB/s]
//      php_manual_en.chm:  15%|███████▏                                        | 2.06M/13.7M [00:02<00:14, 783kB/s]
//
//      $ ipython multi_curl_progress_advanced_watch_curses.py
//       56% [=====================>                  ]
//       23% [========>                               ]
//       15% [=====>                                  ]
//
// Note: The server needs to send a content-length header for progress updates to work.

use Curl\MultiCurl;

// Keep track of download progress for each of the downloads.
$download_status = [];

// Keep track of when the screen was last updated so it not updated too frequently.
$last_updated_time = 0;

$multi_curl = new MultiCurl();

$urls_to_download = [
    'https://www.php.net/distributions/manual/php_manual_en.html.gz',
    'https://www.php.net/distributions/manual/php_manual_en.tar.gz',
    'https://www.php.net/distributions/manual/php_manual_en.chm',
];

$i = 0;
foreach ($urls_to_download as $url) {
    $filename = basename($url);
    echo 'will be downloading ' . $url . ' and saving as "' . $filename . '"' . "\n";

    $download_status[$i] = [
        'position' => $i,
        'complete' => false,
        'filename' => $filename,
        'size' => 0,
        'downloaded' => 0,
    ];

    $curl = $multi_curl->addDownload($url, $filename);

    // Increase timeout to avoid error:
    //   "Operation timed out after 30000 milliseconds with ... out of ... bytes
    //   received".
    $curl->setTimeout(500);

    // Slow the download. Comment the following lines to remove the download
    // throttling.
    $curl->setOpt(CURLOPT_MAX_RECV_SPEED_LARGE, 500000);
    $curl->setOpt(CURLOPT_BUFFERSIZE, 1024);

    $curl->progress(function (
        $client,
        $download_size,
        $downloaded,
        $upload_size,
        $uploaded
    ) use (
        $i,
        &$download_status,
        &$last_updated_time
    ) {
        if ($download_size === 0) {
            return 0;
        }

        $download_completed = $downloaded === $download_size;
        $current_time = time();

        // Avoid sending an update if we're within the same second and the
        // download has not yet completed.
        if (!$download_completed && $current_time === $last_updated_time) {
            return 0;
        }

        $last_updated_time = $current_time;

        // Update progress of this download.
        $download_status[$i]['complete'] = $download_completed;
        $download_status[$i]['size'] = $download_size;
        $download_status[$i]['downloaded'] = $downloaded;

        // Generate response including completion status of all downloads and
        // status of each individual download.
        $response = [
            'status' => '',
            'downloads' => [],
        ];
        $all_downloads_completed = true;
        foreach ($download_status as $key => $value) {
            $response['downloads'][] = $value;
            $all_downloads_completed = $all_downloads_completed && $value['complete'];
        }
        $response['status'] = $all_downloads_completed ? 'done' : 'active';
        $json_response = json_encode($response);

        $out = fopen('/tmp/myfifo', 'w');

        // TODO: Catch broken pipe:
        //   PHP Notice:  fwrite(): Write of 52 bytes failed with errno=32
        //   Broken pipe in ./multi_curl_progress_advanced.php on line [...]
        fwrite($out, $json_response . "\n");

        fclose($out);

        // Comment the following line to hide the download progress updates
        // being sent to the named pipe.
        echo $json_response . "\n";

        return 0;
    });

    $i += 1;
}

echo 'starting download' . "\n";
$multi_curl->start();

echo 'all done' . "\n";
