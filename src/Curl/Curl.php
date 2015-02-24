<?php

namespace Curl;

class Curl
{
    const VERSION = '3.2.3';
    const DEFAULT_TIMEOUT = 30;

    public $curl;
    public $id = null;

    public $error = false;
    public $error_code = 0;
    public $error_message = null;

    public $curl_error = false;
    public $curl_error_code = 0;
    public $curl_error_message = null;

    public $http_error = false;
    public $http_status_code = 0;
    public $http_error_message = null;

    public $base_url = null;
    public $url = null;
    public $request_headers = null;
    public $response_headers = null;
    public $raw_response_headers = '';
    public $response = null;
    public $raw_response = null;

    public $before_send_function = null;
    private $success_function = null;
    private $error_function = null;
    private $complete_function = null;

    private $cookies = array();
    private $headers = array();
    private $options = array();

    private $json_decoder = null;
    private $json_pattern = '~^application/(?:json|vnd\.api\+json)~i';
    private $xml_pattern = '~^(?:text/|application/(?:atom\+|rss\+)?)xml~i';

    public function __construct($base_url = null)
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->id = 1;
        $this->setDefaultUserAgent();
        $this->setDefaultJsonDecoder();
        $this->setDefaultTimeout();
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADERFUNCTION, array($this, 'headerCallback'));
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->headers = new CaseInsensitiveArray();
        $this->setURL($base_url);
    }

    public function beforeSend($callback)
    {
        $this->before_send_function = $callback;
    }

    public function buildPostData($data)
    {
        if (is_array($data)) {
            if (self::is_array_multidim($data)) {
                if (isset($this->headers['Content-Type']) &&
                    preg_match($this->json_pattern, $this->headers['Content-Type'])) {
                    $json_str = json_encode($data);
                    if (!($json_str === false)) {
                        $data = $json_str;
                    }
                } else {
                    $data = self::http_build_multi_query($data);
                }
            } else {
                $binary_data = false;
                foreach ($data as $key => $value) {
                    // Fix "Notice: Array to string conversion" when $value in
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
                    // that contains an empty array.
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    // Fix "curl_setopt(): The usage of the @filename API for
                    // file uploading is deprecated. Please use the CURLFile
                    // class instead".
                    } elseif (is_string($value) && strpos($value, '@') === 0) {
                        $binary_data = true;
                        if (class_exists('CURLFile')) {
                            $data[$key] = new \CURLFile(substr($value, 1));
                        }
                    } elseif ($value instanceof \CURLFile) {
                        $binary_data = true;
                    }
                }

                if (!$binary_data) {
                    if (isset($this->headers['Content-Type']) &&
                        preg_match($this->json_pattern, $this->headers['Content-Type'])) {
                        $json_str = json_encode($data);
                        if (!($json_str === false)) {
                            $data = $json_str;
                        }
                    } else {
                        $data = http_build_query($data);
                    }
                }
            }
        }

        return $data;
    }

    public function call($function)
    {
        if (is_callable($function)) {
            call_user_func_array($function, array($this));
        }
    }

    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    public function complete($callback)
    {
        $this->complete_function = $callback;
    }

    public function delete($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }
        $this->setURL($url, $data);
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->exec();
    }

    public function download($url, $mixed_filename)
    {
        $callback = false;
        if (is_callable($mixed_filename)) {
            $callback = $mixed_filename;
            $fh = tmpfile();
        } else {
            $filename = $mixed_filename;
            $fh = fopen($filename, 'wb');
        }

        $this->setOpt(CURLOPT_FILE, $fh);
        $this->get($url);

        if ($callback) {
            rewind($fh);
            $callback($this, $fh);
        }

        if (is_resource($fh)) {
            fclose($fh);
        }

        // Fix "PHP Notice: Use of undefined constant STDOUT" when reading the
        // PHP script from stdin.
        if (!defined('STDOUT')) {
            define('STDOUT', null);
        }

        // Reset CURLOPT_FILE with STDOUT to avoid: "curl_exec(): CURLOPT_FILE
        // resource has gone away, resetting to default". Using null causes
        // "curl_setopt(): supplied argument is not a valid File-Handle
        // resource".
        $this->setOpt(CURLOPT_FILE, STDOUT);

        // Reset CURLOPT_RETURNTRANSFER to tell cURL to return subsequent
        // responses as the return value of curl_exec(). Without this,
        // curl_exec() will revert to returning boolean values.
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);

        return ! $this->error;
    }

    public function error($callback)
    {
        $this->error_function = $callback;
    }

    public function exec($ch = null)
    {
        $target = $ch === null ? $this : $ch;

        if (!($ch === null)) {
            $this->raw_response = curl_multi_getcontent($ch);
        } else {
            $this->call($this->before_send_function);
            $this->raw_response = curl_exec($this->curl);
            $this->curl_error_code = curl_errno($this->curl);
        }

        $this->curl_error_message = curl_error($this->curl);
        $this->curl_error = !($this->curl_error_code === 0);
        $this->http_status_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->http_error = in_array(floor($this->http_status_code / 100), array(4, 5));
        $this->error = $this->curl_error || $this->http_error;
        $this->error_code = $this->error ? ($this->curl_error ? $this->curl_error_code : $this->http_status_code) : 0;

        $this->request_headers = $this->parseRequestHeaders(curl_getinfo($this->curl, CURLINFO_HEADER_OUT));
        $this->response_headers = $this->parseResponseHeaders($this->raw_response_headers);
        list($this->response, $this->raw_response) = $this->parseResponse($this->response_headers, $this->raw_response);

        $this->http_error_message = '';
        if ($this->error) {
            if (isset($this->response_headers['Status-Line'])) {
                $this->http_error_message = $this->response_headers['Status-Line'];
            }
        }
        $this->error_message = $this->curl_error ? $this->curl_error_message : $this->http_error_message;

        if (!$this->error) {
            $this->call($this->success_function);
        } else {
            $this->call($this->error_function);
        }

        $this->call($this->complete_function);

        return $this->response;
    }

    public function get($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }
        $this->setURL($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);
        return $this->exec();
    }

    public function getOpt($option)
    {
        return $this->options[$option];
    }

    public function head($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }
        $this->setURL($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        return $this->exec();
    }

    public function headerCallback($ch, $header)
    {
        $this->raw_response_headers .= $header;
        return strlen($header);
    }

    public function options($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }
        $this->setURL($url, $data);
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    public function patch($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }
        $this->setURL($url);
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    public function post($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }

        if (is_array($data) && empty($data)) {
            $this->unsetHeader('Content-Length');
        }

        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    public function put($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $url = $this->base_url;
            $data = $url_mixed;
        } else {
            $url = $url_mixed;
        }
        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            $this->setHeader('Content-Length', strlen($put_data));
        }
        $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        return $this->exec();
    }

    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function setDigestAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, str_replace('+', '%20', http_build_query($this->cookies, '', '; ')));
    }

    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    public function setDefaultJsonDecoder()
    {
        $this->json_decoder = function($response) {
            $json_obj = json_decode($response, false);
            if (!($json_obj === null)) {
                $response = $json_obj;
            }
            return $response;
        };
    }

    public function setDefaultTimeout()
    {
        $this->setTimeout(self::DEFAULT_TIMEOUT);
    }

    public function setDefaultUserAgent()
    {
        $user_agent = 'PHP-Curl-Class/' . self::VERSION . ' (+https://github.com/php-curl-class/php-curl-class)';
        $user_agent .= ' PHP/' . PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/' . $curl_version['version'];
        $this->setUserAgent($user_agent);
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[$key] = $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, array_map(function($value, $key) {
            return $key . ': ' . $value;
        }, $headers, array_keys($headers)));
    }

    public function setJsonDecoder($function)
    {
        if (is_callable($function)) {
            $this->json_decoder = $function;
        }
    }

    public function setOpt($option, $value)
    {
        $required_options = array(
            CURLINFO_HEADER_OUT    => 'CURLINFO_HEADER_OUT',
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->options[$option] = $value;
        return curl_setopt($this->curl, $option, $value);
    }

    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }

    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    public function setURL($url, $data = array())
    {
        $this->base_url = $url;
        $this->url = $this->buildURL($url, $data);
        $this->setOpt(CURLOPT_URL, $this->url);
    }

    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    public function success($callback)
    {
        $this->success_function = $callback;
    }

    public function unsetHeader($key)
    {
        $this->setHeader($key, '');
        unset($this->headers[$key]);
    }

    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    public function __destruct()
    {
        $this->close();
    }

    private function buildURL($url, $data = array())
    {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    private function parseHeaders($raw_headers)
    {
        $raw_headers = preg_split('/\r\n/', $raw_headers, null, PREG_SPLIT_NO_EMPTY);
        $http_headers = new CaseInsensitiveArray();

        for ($i = 1; $i < count($raw_headers); $i++) {
            list($key, $value) = explode(':', $raw_headers[$i], 2);
            $key = trim($key);
            $value = trim($value);
            // Use isset() as array_key_exists() and ArrayAccess are not compatible.
            if (isset($http_headers[$key])) {
                $http_headers[$key] .= ',' . $value;
            } else {
                $http_headers[$key] = $value;
            }
        }

        return array(isset($raw_headers['0']) ? $raw_headers['0'] : '', $http_headers);
    }

    private function parseRequestHeaders($raw_headers)
    {
        $request_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($raw_headers);
        $request_headers['Request-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $request_headers[$key] = $value;
        }
        return $request_headers;
    }

    private function parseResponse($response_headers, $raw_response)
    {
        $response = $raw_response;
        if (isset($response_headers['Content-Type'])) {
            if (preg_match($this->json_pattern, $response_headers['Content-Type'])) {
                $json_decoder = $this->json_decoder;
                $response = $json_decoder($response);
            } elseif (preg_match($this->xml_pattern, $response_headers['Content-Type'])) {
                $xml_obj = @simplexml_load_string($response);
                if (!($xml_obj === false)) {
                    $response = $xml_obj;
                }
            }
        }

        return array($response, $raw_response);
    }

    private function parseResponseHeaders($raw_response_headers)
    {
        $response_header_array = explode("\r\n\r\n", $raw_response_headers);
        $response_header  = '';
        for ($i = count($response_header_array) - 1; $i >= 0; $i--) {
            if (stripos($response_header_array[$i], 'HTTP/') === 0) {
                $response_header = $response_header_array[$i];
                break;
            }
        }

        $response_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($response_header);
        $response_headers['Status-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $response_headers[$key] = $value;
        }
        return $response_headers;
    }

    public static function http_build_multi_query($data, $key = null)
    {
        $query = array();

        if (empty($data)) {
            return $key . '=';
        }

        $is_array_assoc = self::is_array_assoc($data);

        foreach ($data as $k => $value) {
            if (is_string($value) || is_numeric($value)) {
                $brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
                $query[] = urlencode($key === null ? $k : $key . $brackets) . '=' . rawurlencode($value);
            } elseif (is_array($value)) {
                $nested = $key === null ? $k : $key . '[' . $k . ']';
                $query[] = self::http_build_multi_query($value, $nested);
            }
        }

        return implode('&', $query);
    }

    public static function is_array_assoc($array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    public static function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return (bool)count(array_filter($array, 'is_array'));
    }
}

class CaseInsensitiveArray implements \ArrayAccess, \Countable, \Iterator
{
    private $container = array();

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $index = array_search(strtolower($offset), array_keys(array_change_key_case($this->container, CASE_LOWER)));
            if (!($index === false)) {
                $keys = array_keys($this->container);
                unset($this->container[$keys[$index]]);
            }
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists(strtolower($offset), array_change_key_case($this->container, CASE_LOWER));
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        $index = array_search(strtolower($offset), array_keys(array_change_key_case($this->container, CASE_LOWER)));
        if ($index === false) {
            return null;
        }

        $values = array_values($this->container);
        return $values[$index];
    }

    public function count()
    {
        return count($this->container);
    }

    public function current()
    {
        return current($this->container);
    }

    public function next()
    {
        return next($this->container);
    }

    public function key()
    {
        return key($this->container);
    }

    public function valid()
    {
        return !($this->current() === false);
    }

    public function rewind()
    {
        reset($this->container);
    }
}
