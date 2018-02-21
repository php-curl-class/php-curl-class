<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/MultiCurlNoLatency.php';

use Curl\Curl;
use Curl\MultiCurl;

class SomeData
{
    public $foo = 'foo';
    public $bar = 'bar';

    public function __construct($digit)
    {
        $this->foo .= $digit;
        $this->bar .= $digit;
    }
}

function getTestArray($count)
{
    $result = array();
    for ($i = 0; $i < $count; $i++) {
        $o = new SomeData($i);
        $result[] = $o;
    }
    return $result;
}

function test($cnt_times, $cnt_req, $curl, $delay = 0)
{
    echo "----- " . get_class($curl) . ". $cnt_times times $cnt_req requests, data delay $delay s -----\n";
    for ($i = 0; $i < $cnt_times; $i++) {
        $testArray = getTestArray($cnt_req);
        $start = microtime(true);
        $url = 'http://127.0.0.1:8000/';
        /* @var SomeData $item */
        foreach ($testArray as $key => $item) {
            // Emulate collect data or doing something for some time
            usleep($delay * 1000000);
            $params = array('foo' => $item->foo, 'bar' => $item->bar);
            if ($curl instanceof MultiCurl) {
                $curl->addGet($url, $params);
            } elseif ($curl instanceof Curl) {
                $curl->get($url, $params);
            }
        }

        if ($curl instanceof MultiCurl) {
            if ($curl instanceof MultiCurlNoLatency) {
                $curl->wait();
            } else {
                $curl->start();
            }
        }

        $end = number_format(microtime(true) - $start, 3, '.', '');
        $memoryKb = number_format(memory_get_usage(true) / 1024, 2, '.', '');
        $memoryPeakKb = number_format(memory_get_peak_usage(true) / 1024, 2, '.', '');
        echo "$i: $memoryKb Kb ($memoryPeakKb Kb peak) RAM, $end sec.\n";
    }
}

$start = microtime(true);

$concurrency = 25;
$dataDelay = 0.001;
if (isset($argv[3])) {
    if ($argv[3] == '-m') {
        $curl = new MultiCurl();
    } elseif ($argv[3] == '-mnl') {
        $curl = new MultiCurlNoLatency();
    }
    $curl->setConcurrency($concurrency);
} else {
    $curl = new Curl();
}

$completeCount = 0;
$successCount = 0;
$errorCount = 0;
$curl->complete(function (Curl $instance) use (&$completeCount) {
    $completeCount++;
});
$curl->success(function (Curl $instance) use (&$successCount) {
    $successCount++;
});
$curl->error(function (Curl $instance) use (&$errorCount) {
    $errorCount++;
});

test($argv[1], $argv[2], $curl, $dataDelay);

$end = number_format(microtime(true) - $start, 3);
echo "All within $end sec. $completeCount completed, $successCount success, $errorCount error.\n";
