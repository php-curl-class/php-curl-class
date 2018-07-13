<?php

namespace Curl;

class MultiCurl
{
    public $baseUrl = null;
    public $multiCurl;

    private $curls = array();
    private $activeCurls = array();
    private $isStarted = false;
    private $concurrency = 25;
    private $nextCurlId = 0;

    private $beforeSendCallback = null;
    private $successCallback = null;
    private $errorCallback = null;
    private $completeCallback = null;

    private $retry = null;

    private $cookies = array();
    private $headers = array();
    private $options = array();

    private $jsonDecoder = null;
    private $xmlDecoder = null;

    /**
     * Construct
     *
     * @access public
     * @param  $base_url
     */
    public function __construct($base_url = null)
    {
        $this->multiCurl = curl_multi_init();
        $this->headers = new CaseInsensitiveArray();
        $this->setUrl($base_url);
    }

    /**
     * Add Delete
     *
     * @access public
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return object
     */
    public function addDelete($url, $query_parameters = array(), $data = array())
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url, $query_parameters);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        return $curl;
    }

    /**
     * Add Download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return object
     */
    public function addDownload($url, $mixed_filename)
    {
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url);

        // Use tmpfile() or php://temp to avoid "Too many open files" error.
        if (is_callable($mixed_filename)) {
            $callback = $mixed_filename;
            $curl->downloadCompleteCallback = $callback;
            $curl->fileHandle = tmpfile();
        } else {
            $filename = $mixed_filename;
            $curl->downloadCompleteCallback = function ($instance, $fh) use ($filename) {
                file_put_contents($filename, stream_get_contents($fh));
            };
            $curl->fileHandle = fopen('php://temp', 'wb');
        }

        $curl->setOpt(CURLOPT_FILE, $curl->fileHandle);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        return $curl;
    }

    /**
     * Add Get
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addGet($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        return $curl;
    }

    /**
     * Add Head
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addHead($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $curl->setOpt(CURLOPT_NOBODY, true);
        return $curl;
    }

    /**
     * Add Options
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addOptions($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url, $data);
        $curl->removeHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $curl;
    }

    /**
     * Add Patch
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addPatch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl();

        if (is_array($data) && empty($data)) {
            $curl->removeHeader('Content-Length');
        }

        $this->queueHandle($curl);
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        return $curl;
    }

    /**
     * Add Post
     *
     * @access public
     * @param  $url
     * @param  $data
     * @param  $follow_303_with_post
     *     If true, will cause 303 redirections to be followed using GET requests (default: false).
     *     Note: Redirections are only followed if the CURLOPT_FOLLOWLOCATION option is set to true.
     *
     * @return object
     */
    public function addPost($url, $data = '', $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool)$data;
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl();
        $this->queueHandle($curl);

        if (is_array($data) && empty($data)) {
            $curl->removeHeader('Content-Length');
        }

        $curl->setUrl($url);

        /*
         * For post-redirect-get requests, the CURLOPT_CUSTOMREQUEST option must not
         * be set, otherwise cURL will perform POST requests for redirections.
         */
        if (!$follow_303_with_post) {
            $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        }

        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        return $curl;
    }

    /**
     * Add Put
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addPut($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $curl->buildPostData($data);
        if (is_string($put_data)) {
            $curl->setHeader('Content-Length', strlen($put_data));
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        return $curl;
    }

    /**
     * Add Search
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addSearch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $this->queueHandle($curl);
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'SEARCH');
        $put_data = $curl->buildPostData($data);
        if (is_string($put_data)) {
            $curl->setHeader('Content-Length', strlen($put_data));
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        return $curl;
    }

    /**
     * Add Curl
     *
     * Add a Curl instance to the handle queue.
     *
     * @access public
     * @param  $curl
     *
     * @return object
     */
    public function addCurl(Curl $curl)
    {
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Before Send
     *
     * @access public
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendCallback = $callback;
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
     * Complete
     *
     * @access public
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->completeCallback = $callback;
    }

    /**
     * Error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback)
    {
        $this->errorCallback = $callback;
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
        return isset($this->options[$option]) ? $this->options[$option] : null;
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
     * Set Concurrency
     *
     * @access public
     * @param  $concurrency
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
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
        $this->cookies[$key] = $value;
    }

    /**
     * Set Cookies
     *
     * @access public
     * @param  $cookies
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
     * Set Cookie String
     *
     * @access public
     * @param  $string
     */
    public function setCookieString($string)
    {
        $this->setOpt(CURLOPT_COOKIE, $string);
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
     * Set Header
     *
     * Add extra header to include in the request.
     *
     * @access public
     * @param  $key
     * @param  $value
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
     * @param  $headers
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
     * @param  $mixed boolean|callable
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
     * @param  $mixed boolean|callable
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
     * @param  $option
     * @param  $value
     */
    public function setOpt($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Set Opts
     *
     * @access public
     * @param  $options
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
     * Set Retry
     *
     * Number of retries to attempt or decider callable. Maximum number of
     * attempts is $maximum_number_of_retries + 1.
     *
     * @access public
     * @param  $mixed
     */
    public function setRetry($mixed)
    {
        $this->retry = $mixed;
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
     */
    public function setUrl($url)
    {
        $this->baseUrl = $url;
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
     * Start
     *
     * @access public
     */
    public function start()
    {
        if ($this->isStarted) {
            return;
        }

        $this->isStarted = true;

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

            curl_multi_exec($this->multiCurl, $active);

            while (!($info_array = curl_multi_info_read($this->multiCurl)) === false) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    foreach ($this->activeCurls as $key => $curl) {
                        if ($curl->curl === $info_array['handle']) {
                            // Set the error code for multi handles using the "result" key in the array returned by
                            // curl_multi_info_read(). Using curl_errno() on a multi handle will incorrectly return 0
                            // for errors.
                            $curl->curlErrorCode = $info_array['result'];
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

                                // Start new requests before removing the handle of the completed one.
                                while (count($this->curls) >= 1 && count($this->activeCurls) < $this->concurrency) {
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
            }

            if (!$active) {
                $active = count($this->activeCurls);
            }
        } while ($active > 0);

        $this->isStarted = false;
    }

    /**
     * Success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback)
    {
        $this->successCallback = $callback;
    }

    /**
     * Unset Header
     *
     * Remove extra header previously set using Curl::setHeader().
     *
     * @access public
     * @param  $key
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
     * @param  $key
     */
    public function removeHeader($key)
    {
        $this->setHeader($key, '');
    }

    /**
     * Verbose
     *
     * @access public
     * @param  bool $on
     * @param  resource $output
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
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
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

    /**
     * Queue Handle
     *
     * @access private
     * @param  $curl
     */
    private function queueHandle($curl)
    {
        // Use sequential ids to allow for ordered post processing.
        $curl->id = $this->nextCurlId++;
        $curl->childOfMultiCurl = true;
        $this->curls[$curl->id] = $curl;

        $curl->setHeaders($this->headers);
    }

    /**
     * Init Handle
     *
     * @access private
     * @param  $curl
     * @throws \ErrorException
     */
    private function initHandle($curl)
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

        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }

        $this->activeCurls[$curl->id] = $curl;
        $curl->call($curl->beforeSendCallback);
    }
}
