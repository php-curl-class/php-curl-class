<?php
class Curl {
    const USER_AGENT = 'PHP-Curl-Class/1.0 (+https://github.com/php-curl-class/php-curl-class)';

    function __construct() {
        if (!extension_loaded('curl')) {
            throw new ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setopt(CURLOPT_RETURNTRANSFER, TRUE);
    }

    function get($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($data));
        $this->setopt(CURLOPT_HTTPGET, TRUE);
        $this->_exec();
    }

    function post($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_POST, TRUE);
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();
    }

    function put($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();
    }

    function patch($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();
    }

    function delete($url, $data=array()) {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();
    }

    function setBasicAuthentication($username, $password) {
        $this->setopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setopt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    function setUserAgent($user_agent) {
        $this->setopt(CURLOPT_USERAGENT, $user_agent);
    }

    function setReferrer($referrer) {
        $this->setopt(CURLOPT_REFERER, $referrer);
    }

    function setCookie($key, $value) {
        $this->_cookies[$key] = $value;
        $this->setopt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }

    function setOpt($option, $value) {
        return curl_setopt($this->curl, $option, $value);
    }

    function close() {
        curl_close($this->curl);
    }

    function _exec() {
        $this->response = curl_exec($this->curl);
        $this->error_code = curl_errno($this->curl);
        $this->error_message = curl_error($this->curl);
        $this->error = !($this->error_code === 0);
        return $this->error_code;
    }

    private $_cookies = array();

    public $curl;
    public $error_code = 0;
    public $error_message = NULL;
    public $response = NULL;
}
