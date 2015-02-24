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
$multi_curl = new MultiCurl();

$multi_curl->success(function($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
    echo 'response: ' . $instance->response . "\n";
});
$multi_curl->error(function($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->error_code . "\n";
    echo 'error message: ' . $instance->error_message . "\n";
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

$multi_curl->start();
```

### Available Methods
```php
Curl::__construct($base_url = null)
Curl::__destruct()
Curl::beforeSend($callback)
Curl::buildPostData($data)
Curl::call()
Curl::close()
Curl::complete($callback)
Curl::delete($url, $data = array())
Curl::download($url, $mixed_filename)
Curl::error($callback)
Curl::exec($ch = null)
Curl::get($url, $data = array())
Curl::getOpt($option)
Curl::head($url, $data = array())
Curl::headerCallback($ch, $header)
Curl::options($url, $data = array())
Curl::patch($url, $data = array())
Curl::post($url, $data = array())
Curl::put($url, $data = array())
Curl::setBasicAuthentication($username, $password = '')
Curl::setCookie($key, $value)
Curl::setCookieFile($cookie_file)
Curl::setCookieJar($cookie_jar)
Curl::setDefaultJsonDecoder()
Curl::setDefaultTimeout()
Curl::setDefaultUserAgent()
Curl::setDigestAuthentication($username, $password = '')
Curl::setHeader($key, $value)
Curl::setJsonDecoder($function)
Curl::setOpt($option, $value)
Curl::setReferer($referer)
Curl::setReferrer($referrer)
Curl::setTimeout($seconds)
Curl::setURL($url, $data = array())
Curl::setUserAgent($user_agent)
Curl::success($callback)
Curl::unsetHeader($key)
Curl::verbose($on = true)
Curl::http_build_multi_query($data, $key = null)
Curl::is_array_assoc($array)
Curl::is_array_multidim($array)
```

### Experimental Methods
```php
MultiCurl::__construct()
MultiCurl::__destruct()
MultiCurl::addDelete($url, $data = array())
MultiCurl::addDownload($url, $mixed_filename)
MultiCurl::addGet($url, $data = array())
MultiCurl::addHead($url, $data = array())
MultiCurl::addOptions($url, $data = array())
MultiCurl::addPatch($url, $data = array())
MultiCurl::addPost($url, $data = array())
MultiCurl::addPut($url, $data = array())
MultiCurl::beforeSend($callback)
MultiCurl::close()
MultiCurl::complete($callback)
MultiCurl::error($callback)
MultiCurl::getOpt($option)
MultiCurl::setBasicAuthentication($username, $password = '')
MultiCurl::setCookie($key, $value)
MultiCurl::setCookieFile($cookie_file)
MultiCurl::setCookieJar($cookie_jar)
MultiCurl::setHeader($key, $value)
MultiCurl::setJsonDecoder($function)
MultiCurl::setOpt($option, $value)
MultiCurl::setReferer($referer)
MultiCurl::setReferrer($referrer)
MultiCurl::setTimeout($seconds)
MultiCurl::setUserAgent($user_agent)
MultiCurl::start()
MultiCurl::success($callback)
MultiCurl::unsetHeader($key)
MultiCurl::verbose($on = true)
```
