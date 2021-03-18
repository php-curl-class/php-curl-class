<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$content = <<<EOF
<?php
echo 'hello, world';
EOF;

$curl = new Curl();
$curl->setHeader('Content-Type', 'application/json');
$curl->post('https://api.github.com/gists', [
    'description' => 'PHP-Curl-Class test.',
    'public' => 'true',
    'files' => [
        'Untitled.php' => [
            'content' => $content,
        ],
    ],
]);

echo 'Gist created at ' . $curl->response->html_url . "\n";
