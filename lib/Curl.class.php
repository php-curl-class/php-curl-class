<?php

class Curl {
    const USER_AGENT = 'PHP-Curl-Class/1.0 (+https://github.com/php-curl-class/php-curl-class)';

    private $_cookies = array();
    private $_headers = array();
    private $_options = array();

    private $_multi_parent = FALSE;
    private $_multi_child = FALSE;
    private $_before_send = NULL;
    private $_success = NULL;
    private $_error = NULL;
    private $_complete = NULL;

    public $curl;
    public $curls;

    public $error = FALSE;
    public $error_code = 0;
    public $error_message = NULL;

    public $curl_error = FALSE;
    public $curl_error_code = 0;
    public $curl_error_message = NULL;

    public $http_error = FALSE;
    public $http_status_code = 0;
    public $http_error_message = NULL;

    public $request_headers = NULL;
    public $response_headers = NULL;
    public $response = NULL;

    public function __construct() {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setOpt(CURLINFO_HEADER_OUT, TRUE);
        $this->setOpt(CURLOPT_HEADER, TRUE);
        $this->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
    }

    public function get($url_mixed, $data=array()) {
        if (is_array($url_mixed)) {
            $curl_multi = curl_multi_init();
            $this->_multi_parent = TRUE;

            $this->curls = array();

            foreach ($url_mixed as $url) {
                $curl = new Curl();
                $curl->_multi_child = TRUE;
                $curl->setOpt(CURLOPT_URL, $this->_buildURL($url, $data), $curl->curl);
                $curl->setOpt(CURLOPT_HTTPGET, TRUE);
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
                $this->_exec($ch);
            }
        }
        else {
            $this->setopt(CURLOPT_URL, $this->_buildURL($url_mixed, $data));
            $this->setopt(CURLOPT_HTTPGET, TRUE);
            return $this->_exec();
        }
    }

    public function post($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_POST, TRUE);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($data));
        return $this->_exec();
    }

    public function put($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->_exec();
    }

    public function patch($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->_exec();
    }

    public function delete($url, $data=array()) {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->_exec();
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

    public function setOpt($option, $value, $_ch=NULL) {
        $ch = is_null($_ch) ? $this->curl : $_ch;
        $this->_options[$option] = $value;
        return curl_setopt($ch, $option, $value);
    }

    public function verbose($on=TRUE) {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    public function close() {
        if ($this->_multi_parent) {
            foreach ($this->curls as $curl) {
                curl_close($curl->curl);
            }
        }

        curl_close($this->curl);
    }

    public function beforeSend($function) {
        $this->_before_send = $function;
    }

    private function _http_build_multi_query($data, $key=NULL) {
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
                $query[] = $this->_http_build_multi_query($value, $nested);
            }
        }

        return implode('&', $query);
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

    private function _postfields($data) {
        if (is_array($data)) {
            if (is_array_multidim($data)) {
                $data = $this->_http_build_multi_query($data);
            }
            else {
                // Fix "Notice: Array to string conversion" when $value in
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
                // that contains an empty array.
                foreach ($data as $key => $value) {
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    }
                }
            }
        }

        return $data;
    }

    protected function _exec($_ch=NULL) {
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

        $ch->request_headers = preg_split('/\r\n/', curl_getinfo($ch->curl, CURLINFO_HEADER_OUT), NULL, PREG_SPLIT_NO_EMPTY);
        $ch->response_headers = '';
        if (!(strpos($ch->response, "\r\n\r\n") === FALSE)) {
            list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            if ($response_header === 'HTTP/1.1 100 Continue') {
                list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            }
            $ch->response_headers = preg_split('/\r\n/', $response_header, NULL, PREG_SPLIT_NO_EMPTY);
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

function is_array_assoc($array) {
    return (bool)count(array_filter(array_keys($array), 'is_string'));
}

function is_array_multidim($array) {
    if (!is_array($array)) {
        return FALSE;
    }

    return !(count($array) === count($array, COUNT_RECURSIVE));
}
