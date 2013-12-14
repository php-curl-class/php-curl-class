<?php
class Curl {
    const USER_AGENT = 'PHP-Curl-Class/1.0 (+https://github.com/php-curl-class/php-curl-class)';

    private $_cookies = array();
    private $_headers = array();

    public $curl;

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
            throw new ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setopt(CURLINFO_HEADER_OUT, TRUE);
        $this->setopt(CURLOPT_HEADER, TRUE);
        $this->setopt(CURLOPT_RETURNTRANSFER, TRUE);
    }

    public function get($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($data));
        $this->setopt(CURLOPT_HTTPGET, TRUE);
        return $this->_exec();
    }

    public function post($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_POST, TRUE);
        $this->setopt(CURLOPT_POSTFIELDS, $this->_postfields($data));
        return $this->_exec();
    }

    public function put($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($data));
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PUT');
        return $this->_exec();
    }

    public function patch($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        return $this->_exec();
    }

    public function delete($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($data));
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->_exec();
    }

    public function setBasicAuthentication($username, $password) {
        $this->setopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setopt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function setHeader($key, $value) {
        $this->_headers[$key] = $key . ': ' . $value;
        $this->setopt(CURLOPT_HTTPHEADER, array_values($this->_headers));
    }

    public function setUserAgent($user_agent) {
        $this->setopt(CURLOPT_USERAGENT, $user_agent);
    }

    public function setReferrer($referrer) {
        $this->setopt(CURLOPT_REFERER, $referrer);
    }

    public function setCookie($key, $value) {
        $this->_cookies[$key] = $value;
        $this->setopt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }

    public function setOpt($option, $value) {
        return curl_setopt($this->curl, $option, $value);
    }

    public function verbose($on=TRUE) {
        $this->setopt(CURLOPT_VERBOSE, $on);
    }

    public function close() {
        curl_close($this->curl);
    }

    private function http_build_multi_query($data, $key=NULL) {
        $query = array();

        $is_array_assoc = is_array_assoc($data);

        foreach ($data as $k => $value) {
            if (is_string($value) || is_numeric($value)) {
                $brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
                $query[] = urlencode(is_null($key) ? $k : $key . $brackets) . '=' . rawurlencode($value);
            }
            else if (is_array($value)) {
                $query[] = $this->http_build_multi_query($value, $k);
            }
        }

        return implode('&', $query);
    }

    private function _postfields($data) {
        if (is_array($data)) {
            if (is_array_multidim($data)) {
                $data = $this->http_build_multi_query($data);
            }
            else {
                // Fix "Notice: Array to string conversion" when $value in
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
                // that contains an empty array.
                foreach ($data as &$value) {
                    if (is_array($value) && empty($value)) {
                        $value = '';
                    }
                }
            }
        }

        return $data;
    }

    private function _exec() {
        $this->response = curl_exec($this->curl);
        $this->curl_error_code = curl_errno($this->curl);
        $this->curl_error_message = curl_error($this->curl);
        $this->curl_error = !($this->curl_error_code === 0);
        $this->http_status_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->http_error = in_array(floor($this->http_status_code / 100), array(4, 5));
        $this->error = $this->curl_error || $this->http_error;
        $this->error_code = $this->error ? ($this->curl_error ? $this->curl_error_code : $this->http_status_code) : 0;

        $this->request_headers = preg_split('/\r\n/', curl_getinfo($this->curl, CURLINFO_HEADER_OUT), NULL, PREG_SPLIT_NO_EMPTY);
        $this->response_headers = '';
        if (!(strpos($this->response, "\r\n\r\n") === FALSE)) {
            list($response_header, $this->response) = explode("\r\n\r\n", $this->response, 2);
            if ($response_header === 'HTTP/1.1 100 Continue') {
                list($response_header, $this->response) = explode("\r\n\r\n", $this->response, 2);
            }
            $this->response_headers = preg_split('/\r\n/', $response_header, NULL, PREG_SPLIT_NO_EMPTY);
        }

        $this->http_error_message = $this->error ? (isset($this->response_headers['0']) ? $this->response_headers['0'] : '') : '';
        $this->error_message = $this->curl_error ? $this->curl_error_message : $this->http_error_message;

        return $this->error_code;
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
