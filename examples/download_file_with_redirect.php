<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$start_url = 'https://php.net/images/logos/php-med-trans.png';
$final_url = 'https://www.php.net/images/logos/php-med-trans.png';

$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
$curl->download($start_url, '/tmp/php-med-trans.png');

assert($final_url === $curl->effectiveUrl);
