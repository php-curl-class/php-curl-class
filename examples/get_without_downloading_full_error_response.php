<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->setStop(function ($ch, $header) {
    // Stop requests returning error responses early without downloading the
    // full error response.
    //
    // Check the header for the status line starting with "HTTP/".
    // Status-Line per RFC 2616:
    //   6.1 Status-Line:
    //     Status-Line = HTTP-Version SP Status-Code SP Reason-Phrase CRLF
    if (stripos($header, 'HTTP/') === 0) {
        $status_line_parts = explode(' ', $header);
        if (isset($status_line_parts['1'])) {
            $http_status_code = $status_line_parts['1'];
            $http_error = in_array((int) floor($http_status_code / 100), [4, 5], true);
            if ($http_error) {
                // Return true to stop receiving the response.
                return true;
            }
        }
    }

    // Return false to continue receiving the response.
    return false;
});

$curl->get('https://www.example.com/large-500-error');
if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
    echo 'Response content-length: ' . $curl->responseHeaders['content-length'] . "\n";
    echo 'Actual response size downloaded: ' . $curl->getInfo(CURLINFO_SIZE_DOWNLOAD) . "\n";
} else {
    echo 'Response content-length: ' . $curl->responseHeaders['content-length'] . "\n";
    echo 'Actual response size downloaded: ' . $curl->getInfo(CURLINFO_SIZE_DOWNLOAD) . "\n";
}
