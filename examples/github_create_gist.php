<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$content = <<<EOF
<?php
echo 'hello, world';
EOF;

$curl = new Curl();
$curl->post('https://api.github.com/gists', json_encode(array(
    'description' => 'PHP-Curl-Class test.',
    'public' => 'true',
    'files' => array(
        'Untitled.php' => array(
            'content' => $content,
        ),
    ),
)));

echo 'Gist created at ' . $curl->response->html_url . "\n";
