<?php
// keywords:
//   Django: MultiValueDict, QueryDict, request.GET.getlist(), request.POST.getlist()
//   Python: urllib.urlencode, parse.urlencode
//   Java: request.getParameterValues()

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl "https://httpbin.org/post" -d "foo=bar&foo=baz"

function http_build_query_without_indexes($query) {
    $array = [];
    foreach ($query as $key => $value) {
        $key = rawurlencode($key);
        if (is_array($value)) {
            foreach ($value as $v) {
                $v = rawurlencode($v);
                $array[] = $key . '=' . $v;
            }
        } else {
            $value = rawurlencode($value);
            $array[] = $key . '=' . $value;
        }
    }
    return implode('&', $array);
}

$curl = new Curl();
$curl->post('https://httpbin.org/post', http_build_query_without_indexes([
    'foo' => [
        'bar',
        'baz',
    ],
]));

// @codingStandardsIgnoreFile
