---
title: PHP Curl Class Documentation

language_tabs:
  - php

toc_footers:
  - <a href="https://github.com/php-curl-class/php-curl-class" target="_blank">PHP Curl Class on Github</a>
---

# Introduction

PHP Curl Class is an object-oriented wrapper of the PHP cURL extension.

# Install

## Composer

Install using Composer.

```shell
$ composer require php-curl-class/php-curl-class
```

## Include

Include PHP Curl Class.

```php
<?php
require 'Curl/Curl.php';

use \Curl\Curl;
```

# Usage

## GET

```php
<?php
require 'Curl/Curl.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('https://www.example.com/', array(
    'q' => 'keyword',
));
```

## POST

```php
<?php
$curl = new Curl();
$curl->post('https://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));
```

## PUT

```php
<?php
$curl = new Curl();
$curl->put('https://api.example.com/user/', array(
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
));
```

## PATCH

```php
<?php
$curl = new Curl();
$curl->patch('https://api.example.com/profile/', array(
    'image' => '@path/to/file.jpg',
));
```

```php
<?php
$curl = new Curl();
$curl->patch('https://api.example.com/profile/', array(
    'image' => new CURLFile('path/to/file.jpg'),
));
```

## DELETE

```php
<?php
$curl = new Curl();
$curl->delete('https://api.example.com/user/', array(
    'id' => '1234',
));
```

## HEAD

```php
<?php
$curl = new Curl();
$curl->head('https://www.example.com/song.mp3');
```

## OPTIONS

```php
<?php
$curl = new Curl();
$curl->options('https://www.example.com/');
```

## Curl Options

```php
<?php
$curl = new Curl();
$curl->setOpt(CURLOPT_AUTOREFERER, true);
$curl->get('https://www.example.com/302');
```

```php
<?php
$curl = new Curl();
// Enable gzip compression.
$curl->setOpt(CURLOPT_ENCODING , 'gzip');
$curl->get('https://www.example.com/image.png');
```

## Authentication

```php
<?php
$curl = new Curl();
$curl->setBasicAuthentication('username', 'password');
```

## User Agent

```php
<?php
$curl = new Curl();
$curl->setUserAgent('');
```

## Referrer

```php
<?php
$curl = new Curl();
$curl->setReferrer('');
```

## Cookies

```php
<?php
$curl = new Curl();
$curl->setCookie('key', 'value');
```

## Request header

```php
<?php
$curl = new Curl();
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->get('https://www.example.com/');

// Case-insensitive access to headers.
echo $curl->request_headers['X-Requested-With'] . "\n"; // XMLHttpRequest
echo $curl->request_headers['x-ReQUeStED-wiTH'] . "\n"; // XMLHttpRequest
```

## Response header

```php
<?php
$curl = new Curl();
$curl->get('https://www.example.com/image.png');

// Case-insensitive access to headers.
echo $curl->response_headers['Content-Type'] . "\n"; // image/png
echo $curl->response_headers['CoNTeNT-TyPE'] . "\n"; // image/png
```

## Response body

```php
<?php
$curl = new Curl();
$curl->get('https://www.example.com/');

echo $curl->response;
```

## Curl object

> Example access to curl object.

```php
<?php
$curl->setOpt(CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
// or
curl_set_opt($curl->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
```

```php
<?php
$curl->close();
// or
curl_close($curl->curl);
```

## Errors
```php
<?php
$curl = new Curl();
$curl->get('https://www.example.com/404');

if ($curl->error) {
    echo 'Error code: ' . $curl->error_code . "\n"; // 404
    echo 'Error message: ' . $curl->error_message . "\n"; // HTTP/1.1 404 Not Found
}
else {
    echo $curl->response;
}
```

## Parallel Requests

```php
<?php
// Requests in parallel with callback functions.
$curl = new Curl();

$curl->success(function($instance) {
    echo 'call was successful. response was' . "\n";
    echo $instance->response . "\n";
});
$curl->error(function($instance) {
    echo 'call was unsuccessful.' . "\n";
    echo 'error code:' . $instance->error_code . "\n";
    echo 'error message:' . $instance->error_message . "\n";
});
$curl->complete(function($instance) {
    echo 'call completed' . "\n";
});

$curl->get(array(
    'https://duckduckgo.com/',
    'https://search.yahoo.com/search',
    'https://www.bing.com/search',
    'http://www.dogpile.com/search/web',
    'https://www.google.com/search',
    'https://www.wolframalpha.com/input/',
), array(
    'q' => 'hello world',
));
```
