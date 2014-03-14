<?php

class Curl {
    const USER_AGENT = 'PHP-Curl-Class/1.0 (+https://github.com/php-curl-class/php-curl-class)';

    private $_cookies = array();
    private $_headers = array();
    private $_options = array();

    private $_multi_parent = false;
    private $_multi_child = false;
    private $_before_send = null;
    private $_success = null;
    private $_error = null;
    private $_complete = null;

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

    public $request_headers = null;
    public $response_headers = null;
    public $response = null;

    public function __construct() {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADER, true);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    public function get($url_mixed, $data=array()) {
        if (is_array($url_mixed)) {
            $curl_multi = curl_multi_init();
            $this->_multi_parent = true;

            $this->curls = array();

            foreach ($url_mixed as $url) {
                $curl = new Curl();
                $curl->_multi_child = true;
                $curl->setOpt(CURLOPT_URL, $this->_buildURL($url, $data), $curl->curl);
                $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
                $curl->setOpt(CURLOPT_HTTPGET, true);
                $this->_call($this->_before_send, $curl);
                $this->curls[] = $curl;

                $curlm_error_code = curl_multi_add_handle($curl_multi, $curl->curl);
                if (!($curlm_error_code === CURLM_OK)) {
                    throw new \ErrorException('cURL multi add handle error: ' .
                        curl_multi_strerror($curlm_error_code));
                }
            }

            foreach ($this->curls as $ch) {
                foreach ($this->_options as $key => $value) {
                    $ch->setOpt($key, $value);
                }
            }

            do {
                $status = curl_multi_exec($curl_multi, $active);
            } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

            foreach ($this->curls as $ch) {
                $this->exec($ch);
            }
        }
        else {
            $this->setopt(CURLOPT_URL, $this->_buildURL($url_mixed, $data));
            $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
            $this->setopt(CURLOPT_HTTPGET, true);
            return $this->exec();
        }
    }

    public function post($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($data));
        return $this->exec();
    }

    public function put($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->exec();
    }

    public function patch($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    public function delete($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->exec();
    }

    public function setBasicAuthentication($username, $password) {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function setHeader($key, $value) {
        $this->_headers[$key] = $key . ': ' . $value;
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->_headers));
    }

    public function setUserAgent($user_agent) {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    public function setReferrer($referrer) {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    public function setCookie($key, $value) {
        $this->_cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }

    public function setCookieFile($cookie_file) {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    public function setCookieJar($cookie_jar) {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    public function setOpt($option, $value, $_ch=null) {
        $ch = is_null($_ch) ? $this->curl : $_ch;

        $required_options = array(
            CURLINFO_HEADER_OUT    => 'CURLINFO_HEADER_OUT',
            CURLOPT_HEADER         => 'CURLOPT_HEADER',
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->_options[$option] = $value;
        return curl_setopt($ch, $option, $value);
    }

    public function verbose($on=true) {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    public function close() {
        if ($this->_multi_parent) {
            foreach ($this->curls as $curl) {
                $curl->close();
            }
        }

        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    public function beforeSend($function) {
        $this->_before_send = $function;
    }

    public function success($callback) {
        $this->_success = $callback;
    }

    public function error($callback) {
        $this->_error = $callback;
    }

    public function complete($callback) {
        $this->_complete = $callback;
    }

    private function _buildURL($url, $data=array()) {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    private function _parseHeaders($raw_headers) {
        $raw_headers = preg_split('/\r\n/', $raw_headers, null, PREG_SPLIT_NO_EMPTY);
        $http_headers = new CaseInsensitiveArray();

        for ($i = 1; $i < count($raw_headers); $i++) {
            list($key, $value) = explode(':', $raw_headers[$i], 2);
            $key = trim($key);
            $value = trim($value);
            if (array_key_exists($key, $http_headers)) {
                $http_headers[$key] .= ',' . $value;
            }
            else {
                $http_headers[$key] = $value;
            }
        }

        return $http_headers;
    }

    private function _postfields($data) {
        if (is_array($data)) {
            if (is_array_multidim($data)) {
                $data = http_build_multi_query($data);
            }
            else {
                foreach ($data as $key => $value) {
                    // Fix "Notice: Array to string conversion" when $value in
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
                    // that contains an empty array.
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    }
                    // Fix "curl_setopt(): The usage of the @filename API for
                    // file uploading is deprecated. Please use the CURLFile
                    // class instead".
                    else if (is_string($value) && strpos($value, '@') === 0) {
                        if (class_exists('CURLFile')) {
                            $data[$key] = new CURLFile(substr($value, 1));
                        }
                    }
                }
            }
        }

        return $data;
    }

    protected function exec($_ch=null) {
        $ch = is_null($_ch) ? $this : $_ch;

        if ($ch->_multi_child) {
            $ch->response = curl_multi_getcontent($ch->curl);
        }
        else {
            $ch->response = curl_exec($ch->curl);
        }

        $ch->curl_error_code = curl_errno($ch->curl);
        $ch->curl_error_message = curl_error($ch->curl);
        $ch->curl_error = !($ch->curl_error_code === 0);
        $ch->http_status_code = curl_getinfo($ch->curl, CURLINFO_HTTP_CODE);
        $ch->http_error = in_array(floor($ch->http_status_code / 100), array(4, 5));
        $ch->error = $ch->curl_error || $ch->http_error;
        $ch->error_code = $ch->error ? ($ch->curl_error ? $ch->curl_error_code : $ch->http_status_code) : 0;

        $ch->request_headers = $this->_parseHeaders(curl_getinfo($ch->curl, CURLINFO_HEADER_OUT));
        $ch->response_headers = '';
        if (!(strpos($ch->response, "\r\n\r\n") === false)) {
            list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            if ($response_header === 'HTTP/1.1 100 Continue') {
                list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            }
            $ch->response_headers = $this->_parseHeaders($response_header);
        }

        $ch->http_error_message = $ch->error ? (isset($ch->response_headers['0']) ? $ch->response_headers['0'] : '') : '';
        $ch->error_message = $ch->curl_error ? $ch->curl_error_message : $ch->http_error_message;

        if (!$ch->error) {
            $ch->_call($this->_success, $ch);
        }
        else {
            $ch->_call($this->_error, $ch);
        }

        $ch->_call($this->_complete, $ch);

        return $ch->error_code;
    }

    private function _call($function) {
        if (is_callable($function)) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array($function, $args);
        }
    }

    public function __destruct() {
        $this->close();
    }
}

class CaseInsensitiveArray implements ArrayAccess, Countable {
    private $container = array();

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        }
        else {
            $index = array_search(strtolower($offset), array_keys(array_change_key_case($this->container, CASE_LOWER)));
            if (!($index === false)) {
                unset($this->container[array_keys($this->container)[$index]]);
            }
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return array_key_exists(strtolower($offset), array_change_key_case($this->container, CASE_LOWER));
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        $index = array_search(strtolower($offset), array_keys(array_change_key_case($this->container, CASE_LOWER)));
        return $index === false ? null : array_values($this->container)[$index];
    }

    public function count() {
        return count($this->container);
    }
}

function is_array_assoc($array) {
    return (bool)count(array_filter(array_keys($array), 'is_string'));
}

function is_array_multidim($array) {
    if (!is_array($array)) {
        return false;
    }

    return !(count($array) === count($array, COUNT_RECURSIVE));
}

function http_build_multi_query($data, $key=null) {
    $query = array();

    if (empty($data)) {
        return $key . '=';
    }

    $is_array_assoc = is_array_assoc($data);

    foreach ($data as $k => $value) {
        if (is_string($value) || is_numeric($value)) {
            $brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
            $query[] = urlencode(is_null($key) ? $k : $key . $brackets) . '=' . rawurlencode($value);
        }
        else if (is_array($value)) {
            $nested = is_null($key) ? $k : $key . '[' . $k . ']';
            $query[] = http_build_multi_query($value, $nested);
        }
    }

    return implode('&', $query);
}
