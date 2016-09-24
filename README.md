# PHP Curl Class: HTTP requests made easy

[![Build Status](https://api.travis-ci.org/php-curl-class/php-curl-class.svg)](https://travis-ci.org/php-curl-class/php-curl-class)
[![Downloads](https://img.shields.io/packagist/dt/php-curl-class/php-curl-class.svg)](https://packagist.org/packages/php-curl-class/php-curl-class)

[![License](https://img.shields.io/packagist/l/php-curl-class/php-curl-class.svg)](https://github.com/php-curl-class/php-curl-class/blob/master/LICENSE)

PHP Curl Class is an object-oriented wrapper of the PHP cURL extension that makes it easy to send HTTP requests and integrate with web APIs.

---

- [Installation](#installation)
- [Requirements](#requirements)
- [Quick Start and Examples](#quick-start-and-examples)
- [Available Methods](#available-methods)
- [Contribute](#contribute)

---

### Installation

To install PHP Curl Class, simply:

    $ composer require php-curl-class/php-curl-class

For latest commit version:

    $ composer require php-curl-class/php-curl-class @dev

### Requirements

PHP Curl Class works with PHP 5.3, 5.4, 5.5, 5.6, 7.0, 7.1, and HHVM.

### Quick Start and Examples

More examples are available under [/examples](https://github.com/php-curl-class/php-curl-class/tree/master/examples).

```php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('https://www.example.com/');
```

```php
$curl = new Curl();
$curl->get('https://www.example.com/search', array(
    'q' => 'keyword',
));
```

```php
$curl = new Curl();
$curl->post('https://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));
```

```php
$curl = new Curl();
$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('MyUserAgent/0.0.1 (+https://www.example.com/bot.html)');
$curl->setReferrer('');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setCookie('key', 'value');
$curl->get('https://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}

var_dump($curl->requestHeaders);
var_dump($curl->responseHeaders);
```

```php
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
$curl->get('https://shortn.example.com/bHbVsP');
```

```php
$curl = new Curl();
$curl->put('https://api.example.com/user/', array(
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
));
```

```php
$curl = new Curl();
$curl->patch('https://api.example.com/profile/', array(
    'image' => '@path/to/file.jpg',
));
```

```php
$curl = new Curl();
$curl->patch('https://api.example.com/profile/', array(
    'image' => new CURLFile('path/to/file.jpg'),
));
```

```php
$curl = new Curl();
$curl->delete('https://api.example.com/user/', array(
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
echo $curl->responseHeaders['Content-Type'] . "\n"; // image/png
echo $curl->responseHeaders['CoNTeNT-TyPE'] . "\n"; // image/png
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
require __DIR__ . '/vendor/autoload.php';

use \Curl\MultiCurl;

// Requests in parallel with callback functions.
$multi_curl = new MultiCurl();

$multi_curl->success(function($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
    echo 'response:' . "\n";
    var_dump($instance->response);
});
$multi_curl->error(function($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});
$multi_curl->complete(function($instance) {
    echo 'call completed' . "\n";
});

$multi_curl->addGet('https://www.google.com/search', array(
    'q' => 'hello world',
));
$multi_curl->addGet('https://duckduckgo.com/', array(
    'q' => 'hello world',
));
$multi_curl->addGet('https://www.bing.com/search', array(
    'q' => 'hello world',
));

$multi_curl->start(); // Blocks until all items in the queue have been processed.
```

More examples are available under [/examples](https://github.com/php-curl-class/php-curl-class/tree/master/examples).

### Available Methods
```php
Curl::__construct($base_url = null)
Curl::__destruct()
Curl::__get($name)
Curl::beforeSend($callback)
Curl::buildPostData($data)
Curl::call()
Curl::close()
Curl::complete($callback)
Curl::delete($url, $query_parameters = array(), $data = array())
Curl::download($url, $mixed_filename)
Curl::error($callback)
Curl::exec($ch = null)
Curl::get($url, $data = array())
Curl::getCookie($key)
Curl::getInfo($opt)
Curl::getOpt($option)
Curl::getResponseCookie($key)
Curl::head($url, $data = array())
Curl::headerCallback($ch, $header)
Curl::options($url, $data = array())
Curl::patch($url, $data = array())
Curl::post($url, $data = array(), $follow_303_with_post = false)
Curl::progress($callback)
Curl::put($url, $data = array())
Curl::search($url, $data = array())
Curl::setBasicAuthentication($username, $password = '')
Curl::setConnectTimeout($seconds)
Curl::setCookie($key, $value)
Curl::setCookieFile($cookie_file)
Curl::setCookieJar($cookie_jar)
Curl::setCookieString($string)
Curl::setDefaultDecoder($decoder = 'json')
Curl::setDefaultJsonDecoder()
Curl::setDefaultTimeout()
Curl::setDefaultUserAgent()
Curl::setDefaultXmlDecoder()
Curl::setDigestAuthentication($username, $password = '')
Curl::setHeader($key, $value)
Curl::setJsonDecoder($function)
Curl::setMaxFilesize($bytes)
Curl::setOpt($option, $value)
Curl::setOpts($options)
Curl::setPort($port)
Curl::setReferer($referer)
Curl::setReferrer($referrer)
Curl::setTimeout($seconds)
Curl::setURL($url, $data = array())
Curl::setUserAgent($user_agent)
Curl::setXmlDecoder($function)
Curl::success($callback)
Curl::unsetHeader($key)
Curl::verbose($on = true, $output = STDERR)
Curl::array_flatten_multidim($array, $prefix = false)
Curl::is_array_assoc($array)
Curl::is_array_multidim($array)
MultiCurl::__construct($base_url = null)
MultiCurl::__destruct()
MultiCurl::addDelete($url, $query_parameters = array(), $data = array())
MultiCurl::addDownload($url, $mixed_filename)
MultiCurl::addGet($url, $data = array())
MultiCurl::addHead($url, $data = array())
MultiCurl::addOptions($url, $data = array())
MultiCurl::addPatch($url, $data = array())
MultiCurl::addPost($url, $data = array(), $follow_303_with_post = false)
MultiCurl::addPut($url, $data = array())
MultiCurl::addSearch($url, $data = array())
MultiCurl::beforeSend($callback)
MultiCurl::close()
MultiCurl::complete($callback)
MultiCurl::error($callback)
MultiCurl::getOpt($option)
MultiCurl::setBasicAuthentication($username, $password = '')
MultiCurl::setConcurrency($concurrency)
MultiCurl::setConnectTimeout($seconds)
MultiCurl::setCookie($key, $value)
MultiCurl::setCookieFile($cookie_file)
MultiCurl::setCookieJar($cookie_jar)
MultiCurl::setCookieString($string)
MultiCurl::setDigestAuthentication($username, $password = '')
MultiCurl::setHeader($key, $value)
MultiCurl::setJsonDecoder($function)
MultiCurl::setOpt($option, $value)
MultiCurl::setOpts($options)
MultiCurl::setPort($port)
MultiCurl::setReferer($referer)
MultiCurl::setReferrer($referrer)
MultiCurl::setTimeout($seconds)
MultiCurl::setURL($url)
MultiCurl::setUserAgent($user_agent)
MultiCurl::setXmlDecoder($function)
MultiCurl::start()
MultiCurl::success($callback)
MultiCurl::unsetHeader($key)
MultiCurl::verbose($on = true, $output = STDERR)
```

### Contribute
1. Check for open issues or open a new issue to start a discussion around a bug or feature.
1. Fork the repository on GitHub to start making your changes.
1. Write one or more tests for the new feature or that expose the bug.
1. Make code changes to implement the feature or fix the bug.
1. Send a pull request to get your changes merged and published.
