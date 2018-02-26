<?php
require __DIR__ . '/../../vendor/autoload.php';

use Curl\Curl;
use Curl\MultiCurl;

$start = microtime(true);

// Get args from cmd
$countTimes = $argv[1];
$countRequests = $argv[2];
$isMulti = (isset($argv[3]) && $argv[3] === '-m');
$isWaited = (isset($argv[4]) && $argv[4] === '-w');

$concurrency = 25;
$dataDelay = 0.001;

// Create Curl or MultiCurl instance
if ($isMulti) {
    $curl = new MultiCurl();
    $curl->setConcurrency($concurrency);
    if ($isWaited) {
        $curl->waitForStart();
    }
} else {
    $curl = new Curl();
}

// Callback counters
$beforeSendCount = 0;
$completeCount = 0;
$successCount = 0;
$errorCount = 0;
$curl->beforeSend(function (Curl $instance) use (&$beforeSendCount) {
    $beforeSendCount++;
});
$curl->complete(function (Curl $instance) use (&$completeCount) {
    $completeCount++;
});
$curl->success(function (Curl $instance) use (&$successCount) {
    $successCount++;
});
$curl->error(function (Curl $instance) use (&$errorCount) {
    $errorCount++;
});

$message = "----- ";
$message .= get_class($curl);
if ($curl instanceof MultiCurl) {
    $message .= $isWaited ? " with waiting" : " without waiting";
}
$message .= ". $countTimes times $countRequests requests, data delay $dataDelay s";
$message .= " -----\n";
echo $message;

// Params for requests
$paramsArray = array();
for ($j = 0; $j < $countRequests; $j++) {
    $paramsArray[] = array(
        'foo' => 'foo' . $j,
        'bar' => 'bar' . $j,
    );
}

// Go
for ($i = 0; $i < $countTimes; $i++) {
    $start = microtime(true);
    $url = 'http://127.0.0.1:8000/';
    foreach ($paramsArray as $key => $item) {
        // Emulate collect data or doing something for some time
        usleep($dataDelay * 1000000);
        $params = array('foo' => $item['foo'], 'bar' => $item['bar']);
        if ($curl instanceof MultiCurl) {
            $curl->addGet($url, $params);
            //$curl->addGet($url);
            //$curl->addHead($url);
            //$curl->addPost($url);
            //$curl->addPut($url);
            //$curl->addPatch($url);
            //$curl->addDelete($url);
            //$curl->addOptions($url);
            //$curl->addSearch($url);
            //$download_file_path = tempnam('/tmp', 'php-curl-class.');
            //$curl->addDownload($url, $download_file_path)->download = true;
        } elseif ($curl instanceof Curl) {
            $curl->get($url, $params);
        }
    }

    if ($curl instanceof MultiCurl) {
        $curl->start();
    }

    $end = number_format(microtime(true) - $start, 3, '.', '');
    $memoryKb = number_format(memory_get_usage(true) / 1024, 2, '.', '');
    $memoryPeakKb = number_format(memory_get_peak_usage(true) / 1024, 2, '.', '');
    echo "$i: $memoryKb Kb ($memoryPeakKb Kb peak) RAM, $end sec.\n";
}

$end = number_format(microtime(true) - $start, 3);
echo "All within $end sec. $completeCount completed, $successCount success, $errorCount error." .
    " $beforeSendCount before send.\n";
