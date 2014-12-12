# php-curl-class

[![Build Status](https://travis-ci.org/php-curl-class/php-curl-class.png?branch=master)](https://travis-ci.org/php-curl-class/php-curl-class)

PHP Curl Class is an object-oriented wrapper of the PHP cURL extension.

### Composer

    $ composer require php-curl-class/php-curl-class

### Quick Start and Examples

```php
require 'Curl.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('http://www.example.com/');
```

```php
$curl = new Curl();
$curl->get('http://www.example.com/search', array(
    'q' => 'keyword',
));
```

```php
$curl = new Curl();
$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));
```

```php
$curl = new Curl();
$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('');
$curl->setReferrer('');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setCookie('key', 'value');
$curl->get('http://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->error_code . ': ' . $curl->error_message;
}
else {
    echo $curl->response;
}

var_dump($curl->request_headers);
var_dump($curl->response_headers);
```

```php
$curl = new Curl();
$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
$curl->get('https://encrypted.example.com/');
```

```php
$curl = new Curl();
$curl->put('http://api.example.com/user/', array(
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
));
```

```php
$curl = new Curl();
$curl->patch('http://api.example.com/profile/', array(
    'image' => '@path/to/file.jpg',
));
```

```php
$curl = new Curl();
$curl->patch('http://api.example.com/profile/', array(
    'image' => new CURLFile('path/to/file.jpg'),
));
```

```php
$curl = new Curl();
$curl->delete('http://api.example.com/user/', array(
    'id' => '1234',
));
```

```php
// Enable gzip compression and download a file.
$curl = new Curl();
$curl->setOpt(CURLOPT_ENCODING , 'gzip');
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
```

```php
// Case-insensitive access to headers.
$curl = new Curl();
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
echo $curl->response_headers['Content-Type'] . "\n"; // image/png
echo $curl->response_headers['CoNTeNT-TyPE'] . "\n"; // image/png
```

```php
$curl->close();
```

```php
// Example access to curl object.
curl_set_opt($curl->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
curl_close($curl->curl);
```

```php
// Requests in parallel with callback functions.
$curl = new Curl();
$curl->setUserAgent('Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');

$curl->success(function($instance) {
    echo 'call to "' . $instance->url . '" was successful. response was' . "\n";
    echo $instance->response . "\n";
});
$curl->error(function($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
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
