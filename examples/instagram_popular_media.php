<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const INSTAGRAM_CLIENT_ID = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
const INSTAGRAM_CLIENT_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

session_start();

$redirect_uri = implode('', [
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
    '://',
    $_SERVER['SERVER_NAME'],
    $_SERVER['SCRIPT_NAME'],
]);

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $curl = new Curl();
    $curl->post('https://api.instagram.com/oauth/access_token', [
        'client_id' => INSTAGRAM_CLIENT_ID,
        'client_secret' => INSTAGRAM_CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirect_uri,
        'code' => $code,
    ]);

    if ($curl->error) {
        echo $curl->response->error_type . ': ' . $curl->response->errorMessage . '<br />';
        echo '<a href="?">Try again?</a>';
        exit;
    }

    $_SESSION['access_token'] = $curl->response->access_token;
}

if (isset($_SESSION['access_token'])) {
    $curl = new Curl();
    $curl->get('https://api.instagram.com/v1/media/popular', [
        'access_token' => $_SESSION['access_token'],
    ]);
    foreach ($curl->response->data as $media) {
        echo
            '<a href="' . $media->link . '" target="_blank">' .
                '<img alt="" src="' . $media->images->thumbnail->url . '" />' .
            '</a>';
    }
} else {
    header('Location: https://api.instagram.com/oauth/authorize/?' . http_build_query([
        'client_id' => INSTAGRAM_CLIENT_ID,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
    ]));
    exit;
}
