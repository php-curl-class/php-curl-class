<?php

namespace Curl;

class Curl
{
    const VERSION = '2.1.0';

    private $cookies = array();
    private $headers = array();
    private $options = array();

    private $multi_parent = false;
    private $multi_child = false;
    private $before_send_function = null;
    private $success_function = null;
    private $error_function = null;
    private $complete_function = null;

    public $curl;
    public $curls;

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
    public $response = null;
    public $raw_response = null;

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->setDefaultUserAgent();
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADER, true);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    public function get($url_mixed, $data = array())
    {
        if (is_array($url_mixed)) {
            $curl_multi = curl_multi_init();
            $this->multi_parent = true;

            $this->curls = array();

            foreach ($url_mixed as $url) {
                $curl = new Curl();
                $curl->multi_child = true;

                $curl->base_url = $url;
                $curl->url = $this->buildURL($url, $data);
                $curl->setOpt(CURLOPT_URL, $curl->url, $curl->curl);
                $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
                $curl->setOpt(CURLOPT_HTTPGET, true);
                $this->call($this->before_send_function, $curl);
                $this->curls[] = $curl;

                $curlm_error_code = curl_multi_add_handle($curl_multi, $curl->curl);
                if (!($curlm_error_code === CURLM_OK)) {
                    throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
                }
            }

            foreach ($this->curls as $ch) {
                foreach ($this->options as $key => $value) {
                    $ch->setOpt($key, $value);
                }
            }

            do {
                $status = curl_multi_exec($curl_multi, $active);
            } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

            while (!($info_array = curl_multi_info_read($curl_multi)) === false) {
                if (!($info_array['msg'] === CURLMSG_DONE)) {
                    continue;
                }
                foreach ($this->curls as $ch) {
                    if ($ch->curl === $info_array['handle']) {
                        $ch->curl_error_code = $info_array['result'];
                        break;
                    }
                }
            }

            foreach ($this->curls as $ch) {
                $this->exec($ch);
            }
        } else {
            $this->base_url = $url_mixed;
            $this->url = $this->buildURL($url_mixed, $data);
            $this->setopt(CURLOPT_URL, $this->url);
            $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
            $this->setopt(CURLOPT_HTTPGET, true);
            return $this->exec();
        }
    }

    public function post($url, $data = array())
    {
        if (is_array($data) && empty($data)) {
            $this->unsetHeader('Content-Length');
        }

        $this->base_url = $url;
        $this->url = $url;
        $this->setOpt(CURLOPT_URL, $this->url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->postfields($data));
        return $this->exec();
    }

    public function put($url, $data = array())
    {
        $this->base_url = $url;
        $this->url = $url;
        $this->setOpt(CURLOPT_URL, $this->url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = http_build_query($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            $this->setHeader('Content-Length', strlen($put_data));
        }
        $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        return $this->exec();
    }

    public function patch($url, $data = array())
    {
        $this->base_url = $url;
        $this->url = $url;
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_URL, $this->url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    public function delete($url, $data = array())
    {
        $this->base_url = $url;
        $this->url = $url;
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_URL, $this->buildURL($this->url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->exec();
    }

    public function head($url, $data = array())
    {
        $this->base_url = $url;
        $this->url = $this->buildURL($url, $data);
        $this->setOpt(CURLOPT_URL, $this->url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        return $this->exec();
    }

    public function options($url, $data = array())
    {
        $this->unsetHeader('Content-Length');
        $this->base_url = $url;
        $this->url = $url;
        $this->setOpt(CURLOPT_URL, $this->buildURL($url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $key . ': ' . $value;
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->headers));
    }

    public function unsetHeader($key)
    {
        $this->setHeader($key, '');
        unset($this->headers[$key]);
    }

    public function setDefaultUserAgent()
    {
        $user_agent = 'PHP-Curl-Class/' . self::VERSION . ' (+https://github.com/php-curl-class/php-curl-class)';
        $user_agent .= ' PHP/' . PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/' . $curl_version['version'];
        $this->setUserAgent($user_agent);
    }

    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
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

    public function setOpt($option, $value, $_ch = null)
    {
        $ch = $_ch === null ? $this->curl : $_ch;

        $required_options = array(
            CURLINFO_HEADER_OUT    => 'CURLINFO_HEADER_OUT',
            CURLOPT_HEADER         => 'CURLOPT_HEADER',
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->options[$option] = $value;
        return curl_setopt($ch, $option, $value);
    }

    public function getOpt($option)
    {
        return $this->options[$option];
    }

    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    public function close()
    {
        if ($this->multi_parent) {
            foreach ($this->curls as $curl) {
                $curl->close();
            }
        }

        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    public function beforeSend($function)
    {
        $this->before_send_function = $function;
    }

    public function success($callback)
    {
        $this->success_function = $callback;
    }

    public function error($callback)
    {
        $this->error_function = $callback;
    }

    public function complete($callback)
    {
        $this->complete_function = $callback;
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

    private function parseResponse($response)
    {
        $response_headers = '';
        $raw_response = $response;
        if (!(strpos($response, "\r\n\r\n") === false)) {
            $response_array = explode("\r\n\r\n", $response);
            for ($i = count($response_array) - 1; $i >= 0; $i--) {
                if (stripos($response_array[$i], 'HTTP/') === 0) {
                    $response_header = $response_array[$i];
                    $response = implode("\r\n\r\n", array_splice($response_array, $i + 1));
                    break;
                }
            }
            $response_headers = explode("\r\n", $response_header);
            if (in_array('HTTP/1.1 100 Continue', $response_headers)) {
                list($response_header, $response) = explode("\r\n\r\n", $response, 2);
            }
            $response_headers = $this->parseResponseHeaders($response_header);
            $raw_response = $response;

            if (isset($response_headers['Content-Type'])) {
                if (preg_match('~^application/(?:json|vnd\.api\+json)~i', $response_headers['Content-Type'])) {
                    $json_obj = json_decode($response, false);
                    if ($json_obj !== null) {
                        $response = $json_obj;
                    }
                } elseif (preg_match('~^(?:text/|application/(?:atom\+|rss\+)?)xml~i', $response_headers['Content-Type'])) {
                    $xml_obj = @simplexml_load_string($response);
                    if (!($xml_obj === false)) {
                        $response = $xml_obj;
                    }
                }
            }
        }

        return array($response_headers, $response, $raw_response);
    }

    private function parseResponseHeaders($raw_headers)
    {
        $response_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($raw_headers);
        $response_headers['Status-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $response_headers[$key] = $value;
        }
        return $response_headers;
    }

    private function postfields($data)
    {
        if (is_array($data)) {
            if (self::is_array_multidim($data)) {
                $data = self::http_build_multi_query($data);
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
                    $data = http_build_query($data);
                }
            }
        }

        return $data;
    }

    protected function exec($_ch = null)
    {
        $ch = $_ch === null ? $this : $_ch;

        if ($ch->multi_child) {
            $ch->raw_response = curl_multi_getcontent($ch->curl);
        } else {
            $ch->raw_response = curl_exec($ch->curl);
            $ch->curl_error_code = curl_errno($ch->curl);
        }

        $ch->curl_error_message = curl_error($ch->curl);
        $ch->curl_error = !($ch->curl_error_code === 0);
        $ch->http_status_code = curl_getinfo($ch->curl, CURLINFO_HTTP_CODE);
        $ch->http_error = in_array(floor($ch->http_status_code / 100), array(4, 5));
        $ch->error = $ch->curl_error || $ch->http_error;
        $ch->error_code = $ch->error ? ($ch->curl_error ? $ch->curl_error_code : $ch->http_status_code) : 0;

        $ch->request_headers = $this->parseRequestHeaders(curl_getinfo($ch->curl, CURLINFO_HEADER_OUT));
        list($ch->response_headers, $ch->response, $ch->raw_response) = $this->parseResponse($ch->raw_response);

        $ch->http_error_message = '';
        if ($ch->error) {
            if (isset($ch->response_headers['Status-Line'])) {
                $ch->http_error_message = $ch->response_headers['Status-Line'];
            }
        }
        $ch->error_message = $ch->curl_error ? $ch->curl_error_message : $ch->http_error_message;

        if (!$ch->error) {
            $ch->call($this->success_function, $ch);
        } else {
            $ch->call($this->error_function, $ch);
        }

        $ch->call($this->complete_function, $ch);

        return $ch->response;
    }

    private function call($function)
    {
        if (is_callable($function)) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array($function, $args);
        }
    }

    public function __destruct()
    {
        $this->close();
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
