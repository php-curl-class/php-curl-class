<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/CurlE.php';
require __DIR__ . '/MultiCurlE.php';

use Curl\MultiCurl;

function test($cnt_times, $cnt_req, MultiCurl $multi_curl)
{
    echo "----- " . get_class($multi_curl) . ". $cnt_times times $cnt_req requests -----\n";
    for ($i = 0; $i < $cnt_times; $i++) {
        $start = microtime(true);
        for ($j = 0; $j <= $cnt_req; $j++) {
            $multi_curl->addGet('http://127.0.0.1:8000/');
        }
        $multi_curl->start();

        $end = number_format(microtime(true) - $start, 3, '.', '');
        $memoryKb = number_format(memory_get_usage(true) / 1024, 2, '.', '');
        $memoryPeakKb = number_format(memory_get_peak_usage(true) / 1024, 2, '.', '');
        echo "$i: $memoryKb Kb ($memoryPeakKb Kb peak) RAM, $end sec.\n";
    }
    $multi_curl->close();
}

$start = microtime(true);

if (isset($argv[3])) {
    $multi_curl = new MultiCurlE();
} else {
    $multi_curl = new MultiCurl();
}
test($argv[1], $argv[2], $multi_curl);

$end = number_format(microtime(true) - $start, 3);
echo "All within $end sec.\n";
