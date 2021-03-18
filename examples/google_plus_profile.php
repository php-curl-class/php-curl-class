<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const CLIENT_ID = 'XXXXXXXXXXXX.apps.googleusercontent.com';
const CLIENT_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXX';

session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange the authorization code for an access token.
    $curl = new Curl();
    $curl->post('https://accounts.google.com/o/oauth2/token', [
        'code' => $code,
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'redirect_uri' => implode('', [
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
            '://',
            $_SERVER['SERVER_NAME'],
            $_SERVER['SCRIPT_NAME'],
        ]),
        'grant_type' => 'authorization_code',
    ]);

    if ($curl->error) {
        echo $curl->response->error . ': ' . $curl->response->error_description;
        exit;
    }

    $_SESSION['access_token'] = $curl->response->access_token;
    header('Location: ?');
    exit;
} elseif (!empty($_SESSION['access_token']) && !isset($_GET['retry'])) {
    // Use the access token to retrieve the profile.
    $curl = new Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'OAuth ' . $_SESSION['access_token']);
    $curl->get('https://www.googleapis.com/plus/v1/people/me');

    if ($curl->error) {
        echo 'Error ' . $curl->response->error->code . ': ' . $curl->response->error->message . '.<br />';
        echo '<a href="?retry">Retry?</a>';
        exit;
    }

    echo 'Hi ' . $curl->response->displayName . '.';
} else {
    $curl = new Curl();
    $curl->get('https://accounts.google.com/o/oauth2/auth', [
        'scope' => 'https://www.googleapis.com/auth/plus.me',
        'redirect_uri' => implode('', [
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
            '://',
            $_SERVER['SERVER_NAME'],
            $_SERVER['SCRIPT_NAME'],
        ]),
        'response_type' => 'code',
        'client_id' => CLIENT_ID,
        'approval_prompt' => 'force',
    ]);

    $url = $curl->responseHeaders['Location'];
    echo '<a href="' . $url . '">Continue</a>';
}
