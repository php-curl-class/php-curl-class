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

    protected static $defaultContentTypeExpressions = array(
        'json' => '~^(?:application|text)/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?~i',
        'xml'  => '~^(?:application|text)/(?:(?:atom|rss)\+)?xml~i'
    );

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

    private $payloadDecoders = array();
    private $payloadEncoders = array();
    private $contentTypeExpressions = array();

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
        return $this->encodeData($this->normalizeContentType(), $data);
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
        $this->payloadDecoders = array();
        $this->payloadEncoders = array();
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
     * Set Default JSON Decoder
     *
     * @access public
     */
    public function setDefaultJsonDecoder()
    {
        $this->setJsonDecoder(NULL);
    }

    /**
     * Set Default XML Decoder
     *
     * @access public
     */
    public function setDefaultXMLDecoder()
    {
        $this->setXMLDecoder(NULL);
    }

    /**
     * Set JSON Decoder
     *
     * @access public
     * @param  $function
     */
    public function setJsonDecoder($callback)
    {
        $this->setContentTypeDecoder('json', $callback);
    }

    /**
     * Set XML Decoder
     *
     * @access public
     * @param  $function
     */
    public function setXMLDecoder($callback)
    {
        $this->setContentTypeDecoder('xml', $callback);
    }

    /**
     * Set Payload Decoder
     *
     * Sets a Callback to handle decoding of a payload type.
     *
     * @param string $type Content Type to decode.
     * @param callable $callback Callback to handel the decoding (NULL to clear)
     *
     * @return void
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function setContentTypeDecoder($type, $callback) {
        if($callback === NULL) {
            unset($this->payloadDecoders[$type]);
        } elseif (is_callable($callback)) {
            $this->payloadDecoders[$type] = $callback;
        } else {
            throw new \Exception("Payload Decoder for Content-Type '$type' ".
                "must be of type Callable.");
        }
    }

    /**
     * Get Content Type Decoder
     *
     * Returns the Decoder Callback for a specified Content Type.
     *
     * @param string $content_type Content Type to decode.
     *
     * @return mixed Decoder
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function getContentTypeDecoder($content_type) {
        $decoder = array($this, '__defaultDecode'.strtoupper($content_type));
        if(isset($this->payloadDecoders[$content_type])) {
            $decoder = $this->payloadDecoders[$content_type];
        }
        return is_callable($decoder) ? $decoder : NULL;
    }

    /**
     * Set Default JSON Decoder
     *
     * @access public
     */
    public function setDefaultContentTypeEncoder($type)
    {
        $this->setContentTypeEncoder($type, NULL);
    }

    /**
     * Set Payload Decoder
     *
     * Sets a Callback to handle decoding of a payload type.
     *
     * @param string $type Content Type to decode.
     * @param callable $callback Callback to handel the decoding (NULL to clear)
     *
     * @return void
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function setContentTypeEncoder($type, $callback) {
        if($callback === NULL) {
            unset($this->payloadEncoders[$type]);
        } elseif (is_callable($callback)) {
            $this->payloadEncoders[$type] = $callback;
        } else {
            throw new \Exception("Payload Encoder for Content-Type '$type' ".
                "must be of type Callable.");
        }
    }

    /**
     * Get Content Type Encoder
     *
     * @param string $content_type Content Type to Decode
     *
     * @return mixed Content Type Encoder
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function getContentTypeEncoder($content_type) {
        $encoder = array($this, '__defaultEncode'.strtoupper($content_type));
        if(isset($this->payloadEncoders[$content_type])) {
            $encoder = $this->payloadEncoders[$content_type];
        }
        return is_callable($encoder) ? $encoder : NULL;
    }

    /**
     * Set Default XML Decoder
     *
     * @access public
     */
    public function setDefaultContentTypeExpression($type)
    {
        $this->setContentTypeExpression($type, NULL);
    }

    /**
     * Set Content Type Expression
     *
     * Sets a Regular Expression to use to match for a specified Content-Type.
     *
     * @param string $type Content Type to decode.
     * @param callable $expression The expression to match for the content-type.
     *
     * @return void
     *
     * @access private
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function setContentTypeExpression($type, $expression) {
        if($expression === NULL) {
            unset($this->contentTypeExpressions[$type]);
        } elseif (is_callable($expression)) {
            $this->contentTypeExpressions[$type] = $expression;
        } else {
            throw new \Exception("Regular Expression for Content-Type '$type' ".
                "must be a valid Regulair Expression.");
        }
    }

    /**
     * Get Content Type Expressions
     *
     * Returns the Regular Expressions to be used to detect various Content-Types.
     *
     * @return array All Regular Expressions for Content-Type Matching.
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function getContentTypeExpressions() {
        return $this->contentTypeExpressions +
            self::$defaultContentTypeExpressions;
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
     * Normalize Content Type
     *
     * Applies Content-Type regulair expressions and returns the first-matching
     * content type.  Supports the addition of new content types.
     *
     * @param mixed $headers (optional) The headers to detect the Content-Type
     *
     * @return string|null The content type, or NULL if no match found.
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function normalizeContentType($headers = null)
    {
        $content_type = '';
        if(!(is_array($headers) || $headers instanceof CaseInsensitiveArray)) {
            $headers = $this->headers;
        }
        if(isset($headers['Content-Type'])) {
            $expressions = $this->getContentTypeExpressions();
            foreach($expressions as $type => $preg_match) {
                if(preg_match($preg_match, $headers['Content-Type'])) {
                    $content_type = $type;
                    break;
                }
            }
        }
        return (string) $content_type;
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
    private function parseResponse($response_headers, $raw_response) {
        return array(
            $this->decodeData(
                $this->normalizeContentType($response_headers), $raw_response
            ),
            $raw_response
        );
    }

    /**
     * Decode Data
     *
     * Parses a given Payload according to the parser assigned to the given
     * Content Type.
     *
     * @param string $content_type Content Type of the Payload
     * @param string $encoded The un-parsed Payload
     *
     * @return mixed[] The parsed Payload, or the RAW payload if error.
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function decodeData($content_type, $encoded) {
        $decoder = $this->getContentTypeDecoder($content_type);
        if (is_callable($decoder)) {
            $decoded = call_user_func($decoder, $encoded);
        } else {
            $decoded = $encoded;
        }
        return $decoded;
    }

    /**
     * Default Decode JSON
     *
     * The default JSON Payload Decoder
     *
     * @param string $encoded Un-decoded Payload
     *
     * @return mixed The decoded payload or the raw payload on error.
     *
     * @access private
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    private function __defaultDecodeJSON($encoded) {
        $decoded = json_decode($encoded, false);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            // This doesn't seem right, this is NOT the JSON data...  :-/
            $decoded = $encoded;
        }
        return $decoded;
    }

    /**
     * Default Decode XML
     *
     * The default XML Payload Decoder.
     *
     * @param string $encoded The un-decoded Payload
     *
     * @return mixed The XML object or RAW payload on error.
     *
     * @access private
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    private function __defaultDecodeXML($encoded) {
        $decoded = @simplexml_load_string($encoded);
        if ($decoded === false) {
            // This doesn't seem right, this is NOT the XML data...  :-/
            $decoded = $encoded;
        }
        return $decoded;
    }

    /**
     * Encode Data
     *
     * Encodes the provided data using the set or default Encoder according to
     * the Content-Type.
     *
     * @param string $content_type The Content-Type to treat the data as.
     * @param mixed $unencoded Unencoded data.
     *
     * @return mixed Encoded data, or Un-Encoded data on error.
     *
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public function encodeData($content_type, $unencoded) {
        $binary_data = false;
        if (is_array($unencoded)) {
            foreach ($unencoded as $key => $value) {
                // Fix "Notice: Array to string conversion" when $value in curl_setopt($ch, CURLOPT_POSTFIELDS,
                // $value) is an array that contains an empty array.
                if (is_array($value) && empty($value)) {
                    $unencoded[$key] = '';
                // Fix "curl_setopt(): The usage of the @filename API for file uploading is deprecated. Please use
                // the CURLFile class instead". Ignore non-file values prefixed with the @ character.
                } elseif (is_string($value) && strpos($value, '@') === 0) {
                    $file = substr($value, 1);
                    if(is_file($file)) {
                        $binary_data = true;
                        if (class_exists('CURLFile')) {
                            $unencoded[$key] = new \CURLFile($file);
                        }
                    }
                } elseif ($value instanceof \CURLFile) {
                    $binary_data = true;
                }
            }
        }

        $encoded = $unencoded;

        if(!$binary_data) {
            $encoder = $this->getContentTypeEncoder($content_type);
            if(is_callable($encoder)) {
                $encoded = call_user_func($encoder, $unencoded);
            }
        }

        return $encoded;
    }

    /**
     * Default Encode JSON
     *
     * The default JSON encoder.
     *
     * @param mixed $unencoded Unencoded Data.
     *
     * @return mixed The encoded JSON data.
     *
     * @access private
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    private function __defaultEncodeJSON($unencoded) {
        if(is_array($unencoded)) {
            $encoded = json_encode($unencoded);
            if ($encoded === false && json_last_error() !== JSON_ERROR_NONE) {
                // This doesn't seem right, this is NOT the JSON data...  :-/
                $encoded = $unencoded;
            }
        } else{
            $encoded = $unencoded;
        }
        return $encoded;
    }

    /**
     * Default Encode
     *
     * The default Post Data Encoder.
     *
     * @param mixed $unencoded Unencoded Data.
     *
     * @return mixed The encoded data, or un-encoded if error or Binary.
     *
     * @access private
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    private function __defaultEncode($unencoded) {
        if (is_array($unencoded)) {
            $encoded = self::http_build_query($unencoded);
        } else {
            $encoded = $unencoded;
        }
        return $encoded;
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
     * Http Build Query
     *
     * @access public
     * @param  $data
     *
     * @return string
     */
    public static function http_build_query($data)
    {
        if (self::is_array_multidim($data)) {
            $query = self::http_build_multi_query($data);
        } else {
            $query = http_build_query($data, '', '&');
        }
        return (string) $query;
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
        $associative = FALSE;
        if (is_array($array)) foreach($array as $k => $r) if (is_string($k)) {
            $associative = TRUE;
            break;
        }
        return (bool) $associative;
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
        $multi = FALSE;
        if (is_array($array)) foreach($array as $k => $r) if (is_array($r)) {
            $multi = TRUE;
            break;
        }
        return (bool) $multi;
    }
}
