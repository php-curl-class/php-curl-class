<?php

namespace Curl;

abstract class CurlCookieConst
{
    private static $RFC2616 = array();
    private static $RFC6265 = array();

    public static function Init() {
        self::$RFC2616 = array_fill_keys(array(
            // RFC2616: "any CHAR except CTLs or separators".
            '!', '#', '$', '%', '&', "'", '*', '+', '-', '.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A',
            'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '|', '~',
        ), true);

        self::$RFC6265 = array_fill_keys(array(
            // RFC6265: "US-ASCII characters excluding CTLs, whitespace DQUOTE, comma, semicolon, and backslash".
            // %x21
            '!',
            // %x23-2B
            '#', '$', '%', '&', "'", '(', ')', '*', '+',
            // %x2D-3A
            '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':',
            // %x3C-5B
            '<', '=', '>', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[',
            // %x5D-7E
            ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q',
            'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~',
        ), true);
    }

    public static function RFC2616() {
        return self::$RFC2616;
    }

    public static function RFC6265() {
        return self::$RFC6265;
    }
}

CurlCookieConst::Init();

class Curl
{
    const VERSION = '4.8.2';
    const DEFAULT_TIMEOUT = 30;

    public $curl;
    public $id = null;

    public $error = false;
    public $errorCode = 0;
    public $errorMessage = null;

    public $curlError = false;
    public $curlErrorCode = 0;
    public $curlErrorMessage = null;

    public $httpError = false;
    public $httpStatusCode = 0;
    public $httpErrorMessage = null;

    public $baseUrl = null;
    public $url = null;
    public $requestHeaders = null;
    public $responseHeaders = null;
    public $rawResponseHeaders = '';
    public $response = null;
    public $rawResponse = null;

    public $beforeSendFunction = null;
    public $downloadCompleteFunction = null;
    private $successFunction = null;
    private $errorFunction = null;
    private $completeFunction = null;

    private $cookies = array();
    private $responseCookies = array();
    private $headers = array();
    private $options = array();

    private $jsonDecoder = null;
    private $jsonPattern = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';
    private $xmlPattern = '~^(?:text/|application/(?:atom\+|rss\+)?)xml~i';

    /**
     * Construct
     *
     * @access public
     * @param  $base_url
     * @throws \ErrorException
     */
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

    /**
     * Before Send
     *
     * @access public
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendFunction = $callback;
    }

    /**
     * Build Post Data
     *
     * @access public
     * @param  $data
     *
     * @return array|string
     */
    public function buildPostData($data)
    {
        if (is_array($data)) {
            if (self::is_array_multidim($data)) {
                if (isset($this->headers['Content-Type']) &&
                    preg_match($this->jsonPattern, $this->headers['Content-Type'])) {
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
                    // Fix "Notice: Array to string conversion" when $value in curl_setopt($ch, CURLOPT_POSTFIELDS,
                    // $value) is an array that contains an empty array.
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    // Fix "curl_setopt(): The usage of the @filename API for file uploading is deprecated. Please use
                    // the CURLFile class instead". Ignore non-file values prefixed with the @ character.
                    } elseif (is_string($value) && strpos($value, '@') === 0 && is_file(substr($value, 1))) {
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
                        preg_match($this->jsonPattern, $this->headers['Content-Type'])) {
                        $json_str = json_encode($data);
                        if (!($json_str === false)) {
                            $data = $json_str;
                        }
                    } else {
                        $data = http_build_query($data, '', '&');
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Call
     *
     * @access public
     */
    public function call()
    {
        $args = func_get_args();
        $function = array_shift($args);
        if (is_callable($function)) {
            array_unshift($args, $this);
            call_user_func_array($function, $args);
        }
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        $this->options = null;
        $this->jsonDecoder = null;
    }

    /**
     * Complete
     *
     * @access public
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->completeFunction = $callback;
    }

    /**
     * Progress
     *
     * @access public
     * @param  $callback
     */
    public function progress($callback)
    {
        $this->setOpt(CURLOPT_PROGRESSFUNCTION, $callback);
        $this->setOpt(CURLOPT_NOPROGRESS, false);
    }

    /**
     * Delete
     *
     * @access public
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return string
     */
    public function delete($url, $query_parameters = array(), $data = array())
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }

        $this->setURL($url, $query_parameters);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Download Complete
     *
     * @access public
     * @param  $fh
     */
    public function downloadComplete($fh)
    {
        if (!$this->error && $this->downloadCompleteFunction) {
            rewind($fh);
            $this->call($this->downloadCompleteFunction, $fh);
            $this->downloadCompleteFunction = null;
        }

        if (is_resource($fh)) {
            fclose($fh);
        }

        // Fix "PHP Notice: Use of undefined constant STDOUT" when reading the
        // PHP script from stdin. Using null causes "Warning: curl_setopt():
        // supplied argument is not a valid File-Handle resource".
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }

        // Reset CURLOPT_FILE with STDOUT to avoid: "curl_exec(): CURLOPT_FILE
        // resource has gone away, resetting to default".
        $this->setOpt(CURLOPT_FILE, STDOUT);

        // Reset CURLOPT_RETURNTRANSFER to tell cURL to return subsequent
        // responses as the return value of curl_exec(). Without this,
        // curl_exec() will revert to returning boolean values.
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return boolean
     */
    public function download($url, $mixed_filename)
    {
        if (is_callable($mixed_filename)) {
            $this->downloadCompleteFunction = $mixed_filename;
            $fh = tmpfile();
        } else {
            $filename = $mixed_filename;
            $fh = fopen($filename, 'wb');
        }

        $this->setOpt(CURLOPT_FILE, $fh);
        $this->get($url);
        $this->downloadComplete($fh);

        return ! $this->error;
    }

    /**
     * Error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback)
    {
        $this->errorFunction = $callback;
    }

    /**
     * Exec
     *
     * @access public
     * @param  $ch
     *
     * @return string
     */
    public function exec($ch = null)
    {
        $this->responseCookies = array();
        if (!($ch === null)) {
            $this->rawResponse = curl_multi_getcontent($ch);
        } else {
            $this->call($this->beforeSendFunction);
            $this->rawResponse = curl_exec($this->curl);
            $this->curlErrorCode = curl_errno($this->curl);
        }
        $this->curlErrorMessage = curl_error($this->curl);
        $this->curlError = !($this->curlErrorCode === 0);
        $this->httpStatusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->httpError = in_array(floor($this->httpStatusCode / 100), array(4, 5));
        $this->error = $this->curlError || $this->httpError;
        $this->errorCode = $this->error ? ($this->curlError ? $this->curlErrorCode : $this->httpStatusCode) : 0;

        // NOTE: CURLINFO_HEADER_OUT set to true is required for requestHeaders
        // to not be empty (e.g. $curl->setOpt(CURLINFO_HEADER_OUT, true);).
        if ($this->getOpt(CURLINFO_HEADER_OUT) === true) {
            $this->requestHeaders = $this->parseRequestHeaders(curl_getinfo($this->curl, CURLINFO_HEADER_OUT));
        }
        $this->responseHeaders = $this->parseResponseHeaders($this->rawResponseHeaders);
        list($this->response, $this->rawResponse) = $this->parseResponse($this->responseHeaders, $this->rawResponse);

        $this->httpErrorMessage = '';
        if ($this->error) {
            if (isset($this->responseHeaders['Status-Line'])) {
                $this->httpErrorMessage = $this->responseHeaders['Status-Line'];
            }
        }
        $this->errorMessage = $this->curlError ? $this->curlErrorMessage : $this->httpErrorMessage;

        if (!$this->error) {
            $this->call($this->successFunction);
        } else {
            $this->call($this->errorFunction);
        }

        $this->call($this->completeFunction);

        return $this->response;
    }

    /**
     * Get
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function get($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setURL($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);
        return $this->exec();
    }

    /**
     * Get Opt
     *
     * @access public
     * @param  $option
     *
     * @return mixed
     */
    public function getOpt($option)
    {
        return $this->options[$option];
    }

    /**
     * Head
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function head($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setURL($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        return $this->exec();
    }

    /**
     * Header Callback
     *
     * @access public
     * @param  $ch
     * @param  $header
     *
     * @return integer
     */
    public function headerCallback($ch, $header)
    {
        if (preg_match('/^Set-Cookie:\s*([^=]+)=([^;]+)/mi', $header, $cookie) == 1) {
            $this->responseCookies[$cookie[1]] = $cookie[2];
        }
        $this->rawResponseHeaders .= $header;
        return strlen($header);
    }

    /**
     * Options
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function options($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setURL($url, $data);
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    /**
     * Patch
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function patch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        if (is_array($data) && empty($data)) {
            $this->unsetHeader('Content-Length');
        }

        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Post
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function post($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Put
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function put($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
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

    /**
     * Set Basic Authentication
     *
     * @access public
     * @param  $username
     * @param  $password
     */
    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Set Digest Authentication
     *
     * @access public
     * @param  $username
     * @param  $password
     */
    public function setDigestAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Set Cookie
     *
     * @access public
     * @param  $key
     * @param  $value
     */
    public function setCookie($key, $value)
    {
        $name_chars = array();
        foreach (str_split($key) as $name_char) {
            if (!array_key_exists($name_char, CurlCookieConst::RFC2616())) {
                $name_chars[] = rawurlencode($name_char);
            } else {
                $name_chars[] = $name_char;
            }
        }

        $value_chars = array();
        foreach (str_split($value) as $value_char) {
            if (!array_key_exists($value_char, CurlCookieConst::RFC6265())) {
                $value_chars[] = rawurlencode($value_char);
            } else {
                $value_chars[] = $value_char;
            }
        }

        $this->cookies[implode('', $name_chars)] = implode('', $value_chars);
        $this->setOpt(CURLOPT_COOKIE, implode('; ', array_map(function($k, $v) {
            return $k . '=' . $v;
        }, array_keys($this->cookies), array_values($this->cookies))));
    }

    /**
     * Get cookie.
     *
     * @access public
     * @param  $key
     */
    public function getCookie($key)
    {
        return $this->getResponseCookie($key);
    }

    /**
     * Get response cookie.
     *
     * @access public
     * @param  $key
     */
    public function getResponseCookie($key)
    {
        return isset($this->responseCookies[$key]) ? $this->responseCookies[$key] : null;
    }

    /**
     * Set Port
     *
     * @access public
     * @param  $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, intval($port));
    }

    /**
     * Set Connect Timeout
     *
     * @access public
     * @param  $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    /**
     * Set Cookie File
     *
     * @access public
     * @param  $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * Set Cookie Jar
     *
     * @access public
     * @param  $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * Set Default JSON Decoder
     *
     * @access public
     */
    public function setDefaultJsonDecoder()
    {
        $this->jsonDecoder = function($response) {
            $json_obj = json_decode($response, false);
            if (!($json_obj === null)) {
                $response = $json_obj;
            }
            return $response;
        };
    }

    /**
     * Set Default Timeout
     *
     * @access public
     */
    public function setDefaultTimeout()
    {
        $this->setTimeout(self::DEFAULT_TIMEOUT);
    }

    /**
     * Set Default User Agent
     *
     * @access public
     */
    public function setDefaultUserAgent()
    {
        $user_agent = 'PHP-Curl-Class/' . self::VERSION . ' (+https://github.com/php-curl-class/php-curl-class)';
        $user_agent .= ' PHP/' . PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/' . $curl_version['version'];
        $this->setUserAgent($user_agent);
    }

    /**
     * Set Header
     *
     * @access public
     * @param  $key
     * @param  $value
     *
     * @return string
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Set JSON Decoder
     *
     * @access public
     * @param  $function
     */
    public function setJsonDecoder($function)
    {
        if (is_callable($function)) {
            $this->jsonDecoder = $function;
        }
    }

    /**
     * Set Opt
     *
     * @access public
     * @param  $option
     * @param  $value
     *
     * @return boolean
     */
    public function setOpt($option, $value)
    {
        $required_options = array(
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->options[$option] = $value;
        return curl_setopt($this->curl, $option, $value);
    }

    /**
     * Set Referer
     *
     * @access public
     * @param  $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }

    /**
     * Set Referrer
     *
     * @access public
     * @param  $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    /**
     * Set Timeout
     *
     * @access public
     * @param  $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Set Url
     *
     * @access public
     * @param  $url
     * @param  $data
     */
    public function setURL($url, $data = array())
    {
        $this->baseUrl = $url;
        $this->url = $this->buildURL($url, $data);
        $this->setOpt(CURLOPT_URL, $this->url);
    }

    /**
     * Set User Agent
     *
     * @access public
     * @param  $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    /**
     * Success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback)
    {
        $this->successFunction = $callback;
    }

    /**
     * Unset Header
     *
     * @access public
     * @param  $key
     */
    public function unsetHeader($key)
    {
        $this->setHeader($key, '');
        unset($this->headers[$key]);
    }

    /**
     * Verbose
     *
     * @access public
     * @param  $on
     */
    public function verbose($on = true, $output=STDERR)
    {
        // Turn off CURLINFO_HEADER_OUT for verbose to work. This has the side
        // effect of causing Curl::requestHeaders to be empty.
        if ($on) {
            $this->setOpt(CURLINFO_HEADER_OUT, false);
        }
        $this->setOpt(CURLOPT_VERBOSE, $on);
        $this->setOpt(CURLOPT_STDERR, $output);
    }

    /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Build Url
     *
     * @access private
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    private function buildURL($url, $data = array())
    {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    /**
     * Parse Headers
     *
     * @access private
     * @param  $raw_headers
     *
     * @return array
     */
    private function parseHeaders($raw_headers)
    {
        $raw_headers = preg_split('/\r\n/', $raw_headers, null, PREG_SPLIT_NO_EMPTY);
        $http_headers = new CaseInsensitiveArray();

        $raw_headers_count = count($raw_headers);
        for ($i = 1; $i < $raw_headers_count; $i++) {
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

    /**
     * Parse Request Headers
     *
     * @access private
     * @param  $raw_headers
     *
     * @return array
     */
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

    /**
     * Parse Response
     *
     * @access private
     * @param  $response_headers
     * @param  $raw_response
     *
     * @return array
     */
    private function parseResponse($response_headers, $raw_response)
    {
        $response = $raw_response;
        if (isset($response_headers['Content-Type'])) {
            if (preg_match($this->jsonPattern, $response_headers['Content-Type'])) {
                $json_decoder = $this->jsonDecoder;
                if (is_callable($json_decoder)) {
                    $response = $json_decoder($response);
                }
            } elseif (preg_match($this->xmlPattern, $response_headers['Content-Type'])) {
                $xml_obj = @simplexml_load_string($response);
                if (!($xml_obj === false)) {
                    $response = $xml_obj;
                }
            }
        }

        return array($response, $raw_response);
    }

    /**
     * Parse Response Headers
     *
     * @access private
     * @param  $raw_response_headers
     *
     * @return array
     */
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

    /**
     * Http Build Multi Query
     *
     * @access public
     * @param  $data
     * @param  $key
     *
     * @return string
     */
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

    /**
     * Is Array Assoc
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_assoc($array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Is Array Multidim
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return (bool)count(array_filter($array, 'is_array'));
    }
}
