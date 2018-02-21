<?php

namespace Curl;

class MultiCurl
{
    public $baseUrl = null;
    public $multiCurl;

    /**
     * @var Curl[]
     */
    protected $curls = array();

    /**
     * @var Curl[]
     */
    protected $activeCurls = array();
    protected $active      = 0;

    protected $concurrency = 25;
    protected $nextCurlId  = 0;

    protected $beforeSendCallback = null;
    protected $successCallback    = null;
    protected $errorCallback      = null;
    protected $completeCallback   = null;

    protected $retry = null;

    protected $cookies = array();
    protected $headers = array();
    protected $options = array();

    protected $jsonDecoder = null;
    protected $xmlDecoder  = null;

    private $isStarted = false;

    /**
     * Construct
     *
     * @access public
     *
     * @param $base_url
     */
    public function __construct($base_url = null)
    {
        $this->multiCurl = curl_multi_init();
        $this->headers = new CaseInsensitiveArray();
        $this->setUrl($base_url);
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
     * @return int
     */
    public function getActiveCount()
    {
        return $this->active;
    }

    /**
     * Before Send
     *
     * @access public
     *
     * @param $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendCallback = $callback;
    }

    /**
     * Complete
     *
     * @access public
     *
     * @param $callback
     */
    public function complete($callback)
    {
        $this->completeCallback = $callback;
    }

    /**
     * Success
     *
     * @access public
     *
     * @param $callback
     */
    public function success($callback)
    {
        $this->successCallback = $callback;
    }

    /**
     * Error
     *
     * @access public
     *
     * @param $callback
     */
    public function error($callback)
    {
        $this->errorCallback = $callback;
    }

    /**
     * Start
     *
     * @access public
     * @throws \ErrorException
     */
    final public function start()
    {
        if ($this->isStarted) {
            return;
        }
        $this->isStarted = true;

        $this->execMultiCurl();

        $this->isStarted = false;
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        foreach ($this->curls as $curl) {
            $curl->close();
        }

        if (is_resource($this->multiCurl)) {
            curl_multi_close($this->multiCurl);
        }
    }

    /**
     * Add Get
     *
     * @access public
     *
     * @param $url
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addGet($url, $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data);
    }

    /**
     * Add Head
     *
     * @access public
     *
     * @param $url
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addHead($url, $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data);
    }

    /**
     * Add Post
     *
     * @access public
     *
     * @param $url
     * @param $data
     * @param $follow_303_with_post
     *     If true, will cause 303 redirections to be followed using POST requests (default: false).
     *     Note: Redirections are only followed if the CURLOPT_FOLLOWLOCATION option is set to true.
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addPost($url, $data = array(), $follow_303_with_post = false)
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data, $follow_303_with_post);
    }

    /**
     * Add Put
     *
     * @access public
     *
     * @param $url
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addPut($url, $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data);
    }

    /**
     * Add Patch
     *
     * @access public
     *
     * @param $url
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addPatch($url, $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data);
    }

    /**
     * Add Delete
     *
     * @access public
     *
     * @param $url
     * @param $query_parameters
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addDelete($url, $query_parameters = array(), $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $query_parameters, $data);
    }

    /**
     * Add Options
     *
     * @access public
     *
     * @param $url
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addOptions($url, $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data);
    }

    /**
     * Add Search
     *
     * @access public
     *
     * @param $url
     * @param $data
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addSearch($url, $data = array())
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $data);
    }

    /**
     * Add Download
     *
     * @access public
     *
     * @param $url
     * @param $mixed_filename
     *
     * @return Curl
     * @throws \ErrorException
     */
    public function addDownload($url, $mixed_filename)
    {
        $method = strtolower(str_replace('add', '', __FUNCTION__));
        return $this->prepareRequest($method, $url, $mixed_filename);
    }

    /**
     * Add Curl
     *
     * Add a Curl instance to the handle queue.
     *
     * @access public
     *
     * @param $curl
     *
     * @return Curl
     */
    public function addCurl(Curl $curl)
    {
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Get Opt
     *
     * @access public
     *
     * @param $option
     *
     * @return mixed
     */
    public function getOpt($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * Set Basic Authentication
     *
     * @access public
     *
     * @param $username
     * @param $password
     */
    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Set Concurrency
     *
     * @access public
     *
     * @param $concurrency
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
    }

    /**
     * Set Digest Authentication
     *
     * @access public
     *
     * @param $username
     * @param $password
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
     *
     * @param $key
     * @param $value
     */
    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
    }

    /**
     * Set Cookies
     *
     * @access public
     *
     * @param $cookies
     */
    public function setCookies($cookies)
    {
        foreach ($cookies as $key => $value) {
            $this->cookies[$key] = $value;
        }
    }

    /**
     * Set Port
     *
     * @access public
     *
     * @param $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, intval($port));
    }

    /**
     * Set Connect Timeout
     *
     * @access public
     *
     * @param $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    /**
     * Set Cookie String
     *
     * @access public
     *
     * @param $string
     */
    public function setCookieString($string)
    {
        $this->setOpt(CURLOPT_COOKIE, $string);
    }

    /**
     * Set Cookie File
     *
     * @access public
     *
     * @param $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * Set Cookie Jar
     *
     * @access public
     *
     * @param $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * Set Header
     *
     * Add extra header to include in the request.
     *
     * @access public
     *
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $this->updateHeaders();
    }

    /**
     * Set Headers
     *
     * Add extra headers to include in the request.
     *
     * @access public
     *
     * @param $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        $this->updateHeaders();
    }

    /**
     * Set JSON Decoder
     *
     * @access public
     *
     * @param $mixed boolean|callable
     */
    public function setJsonDecoder($mixed)
    {
        if ($mixed === false) {
            $this->jsonDecoder = false;
        } elseif (is_callable($mixed)) {
            $this->jsonDecoder = $mixed;
        }
    }

    /**
     * Set XML Decoder
     *
     * @access public
     *
     * @param $mixed boolean|callable
     */
    public function setXmlDecoder($mixed)
    {
        if ($mixed === false) {
            $this->xmlDecoder = false;
        } elseif (is_callable($mixed)) {
            $this->xmlDecoder = $mixed;
        }
    }

    /**
     * Set Opt
     *
     * @access public
     *
     * @param $option
     * @param $value
     */
    public function setOpt($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Set Opts
     *
     * @access public
     *
     * @param $options
     */
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            $this->setOpt($option, $value);
        }
    }

    /**
     * Set Referer
     *
     * @access public
     *
     * @param $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }

    /**
     * Set Referrer
     *
     * @access public
     *
     * @param $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    /**
     * Set Retry
     *
     * Number of retries to attempt or decider callable. Maximum number of
     * attempts is $maximum_number_of_retries + 1.
     *
     * @access public
     *
     * @param $mixed
     */
    public function setRetry($mixed)
    {
        $this->retry = $mixed;
    }

    /**
     * Set Timeout
     *
     * @access public
     *
     * @param $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Set Url
     *
     * @access public
     *
     * @param $url
     */
    public function setUrl($url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Set User Agent
     *
     * @access public
     *
     * @param $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    /**
     * Unset Header
     *
     * Remove extra header previously set using Curl::setHeader().
     *
     * @access public
     *
     * @param $key
     */
    public function unsetHeader($key)
    {
        unset($this->headers[$key]);
    }

    /**
     * Remove Header
     *
     * Remove an internal header from the request.
     * Using `curl -H "Host:" ...' is equivalent to $curl->removeHeader('Host');.
     *
     * @access public
     *
     * @param $key
     */
    public function removeHeader($key)
    {
        $this->setHeader($key, '');
    }

    /**
     * Verbose
     *
     * @access public
     *
     * @param bool $on
     * @param bool|resource $output
     */
    public function verbose($on = true, $output = STDERR)
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
     * @throws \ErrorException
     */
    protected function execMultiCurl()
    {
        $concurrency = $this->concurrency;
        if ($concurrency > count($this->curls)) {
            $concurrency = count($this->curls);
        }

        for ($i = 0; $i < $concurrency; $i++) {
            $this->initHandle(array_shift($this->curls));
        }

        do {
            // Wait for activity on any curl_multi connection when curl_multi_select (libcurl) fails to correctly block.
            // https://bugs.php.net/bug.php?id=63411
            if (curl_multi_select($this->multiCurl) === -1) {
                usleep(100000);
            }

            curl_multi_exec($this->multiCurl, $this->active);

            while (!($info_array = curl_multi_info_read($this->multiCurl)) === false) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    $this->execCurlHandle($info_array['handle'], $info_array['result']);
                }
            }

            if (!$this->active) {
                $this->active = count($this->activeCurls);
            }
        } while ($this->active > 0);
    }

    /**
     * @param $handle
     * @param $result_multi
     *
     * @throws \ErrorException
     */
    protected function execCurlHandle($handle, $result_multi)
    {
        foreach ($this->activeCurls as $key => $curl) {
            if ($curl->curl === $handle) {
                // Set the error code for multi handles using the "result" key in the array returned by
                // curl_multi_info_read(). Using curl_errno() on a multi handle will incorrectly return 0
                // for errors.
                $curl->curlErrorCode = $result_multi;
                $curl->exec($curl->curl);

                if ($curl->attemptRetry()) {
                    // Remove completed handle before adding again in order to retry request.
                    curl_multi_remove_handle($this->multiCurl, $curl->curl);

                    $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
                    if (!($curlm_error_code === CURLM_OK)) {
                        throw new \ErrorException(
                            'cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code)
                        );
                    }
                } else {
                    $curl->execDone();

                    // Remove completed instance from active curls.
                    unset($this->activeCurls[$key]);

                    // Start a new request before removing the handle of the completed one.
                    if (count($this->curls) >= 1) {
                        $this->initHandle(array_shift($this->curls));
                    }
                    curl_multi_remove_handle($this->multiCurl, $curl->curl);

                    // Clean up completed instance.
                    $curl->close();
                }

                break;
            }
        }
    }

    /**
     * @return Curl
     * @throws \ErrorException
     */
    protected function createCurl()
    {
        $curl = new Curl($this->baseUrl, true);
        return $curl;
    }

    /**
     * @return Curl
     * @throws \ErrorException
     */
    protected function prepareRequest()
    {
        $args = func_get_args();
        $method = array_shift($args);

        // Use with base url if first argument isn't url
        if (is_array($args[0])) {
            array_unshift($args, $this->baseUrl);
            array_pop($args);
        }
        // Add exec=false param (must be last) to Curl method.
        array_push($args, false);

        $curl = $this->createCurl();
        $this->queueHandle($curl);
        call_user_func_array(array($curl, $method), $args);

        return $curl;
    }

    /**
     * Queue Handle
     *
     * @access protected
     *
     * @param Curl $curl
     */
    protected function queueHandle($curl)
    {
        // Use sequential ids to allow for ordered post processing.
        $curl->id = $this->nextCurlId++;
        $curl->isChildOfMultiCurl = true;
        $this->curls[$curl->id] = $curl;

        $curl->setHeaders($this->headers);
    }

    /**
     * Init Handle
     *
     * @access protected
     *
     * @param Curl $curl
     *
     * @throws \ErrorException
     */
    protected function initHandle(Curl $curl)
    {
        // Set callbacks if not already individually set.
        if ($curl->beforeSendCallback === null) {
            $curl->beforeSend($this->beforeSendCallback);
        }
        if ($curl->successCallback === null) {
            $curl->success($this->successCallback);
        }
        if ($curl->errorCallback === null) {
            $curl->error($this->errorCallback);
        }
        if ($curl->completeCallback === null) {
            $curl->complete($this->completeCallback);
        }

        // Set decoders if not already individually set.
        if ($curl->jsonDecoder === null) {
            $curl->setJsonDecoder($this->jsonDecoder);
        }
        if ($curl->xmlDecoder === null) {
            $curl->setXmlDecoder($this->xmlDecoder);
        }

        $curl->setOpts($this->options);
        $curl->setRetry($this->retry);
        $curl->setCookies($this->cookies);

        $curl->init();
        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }

        $this->activeCurls[$curl->id] = $curl;
        $curl->call($curl->beforeSendCallback);
    }

    /**
     * Update Headers
     *
     * @access private
     */
    private function updateHeaders()
    {
        foreach ($this->curls as $curl) {
            $curl->setHeaders($this->headers);
        }
    }

}
