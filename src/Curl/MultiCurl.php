<?php

namespace Curl;

use Curl\ArrayUtil;

class MultiCurl
{
    public $baseUrl = null;
    public $multiCurl;

    private $curls = array();
    private $activeCurls = array();
    private $isStarted = false;
    private $concurrency = 25;
    private $nextCurlId = 0;

    private $rateLimit = null;
    private $rateLimitEnabled = false;
    private $rateLimitReached = false;
    private $maxRequests = null;
    private $interval = null;
    private $intervalSeconds = null;
    private $unit = null;
    private $currentStartTime = null;
    private $currentRequestCount = 0;

    private $beforeSendCallback = null;
    private $successCallback = null;
    private $errorCallback = null;
    private $completeCallback = null;

    private $retry = null;

    private $cookies = array();
    private $headers = array();
    private $options = array();
    private $proxies = null;

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
            $curl->downloadCompleteCallback = $mixed_filename;
            $curl->downloadFileName = null;
            $curl->fileHandle = tmpfile();
        } else {
            $filename = $mixed_filename;

            // Use a temporary file when downloading. Not using a temporary file can cause an error when an existing
            // file has already fully completed downloading and a new download is started with the same destination save
            // path. The download request will include header "Range: bytes=$filesize-" which is syntactically valid,
            // but unsatisfiable.
            $download_filename = $filename . '.pccdownload';
            $this->downloadFileName = $download_filename;

            // Attempt to resume download only when a temporary download file exists and is not empty.
            if (is_file($download_filename) && $filesize = filesize($download_filename)) {
                $first_byte_position = $filesize;
                $range = $first_byte_position . '-';
                $curl->setRange($range);
                $curl->fileHandle = fopen($download_filename, 'ab');

                // Move the downloaded temporary file to the destination save path.
                $curl->downloadCompleteCallback = function ($instance, $fh) use ($download_filename, $filename) {
                    // Close the open file handle before renaming the file.
                    if (is_resource($fh)) {
                        fclose($fh);
                    }

                    rename($download_filename, $filename);
                };
            } else {
                $curl->fileHandle = fopen('php://temp', 'wb');
                $curl->downloadCompleteCallback = function ($instance, $fh) use ($filename) {
                    file_put_contents($filename, stream_get_contents($fh));
                };
            }
        }

        $curl->setFile($curl->fileHandle);
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
     * Set File
     *
     * @access public
     * @param  $file
     */
    public function setFile($file)
    {
        $this->setOpt(CURLOPT_FILE, $file);
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
        if (ArrayUtil::isArrayAssoc($headers)) {
            foreach ($headers as $key => $value) {
                $key = trim($key);
                $value = trim($value);
                $this->headers[$key] = $value;
            }
        } else {
            foreach ($headers as $header) {
                list($key, $value) = explode(':', $header, 2);
                $key = trim($key);
                $value = trim($value);
                $this->headers[$key] = $value;
            }
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
     * Set Proxy
     *
     * Set an HTTP proxy to tunnel requests through.
     *
     * @access public
     * @param  $proxy - The HTTP proxy to tunnel requests through. May include port number.
     * @param  $port - The port number of the proxy to connect to. This port number can also be set in $proxy.
     * @param  $username - The username to use for the connection to the proxy.
     * @param  $password - The password to use for the connection to the proxy.
     */
    public function setProxy($proxy, $port = null, $username = null, $password = null)
    {
        $this->setOpt(CURLOPT_PROXY, $proxy);
        if ($port !== null) {
            $this->setOpt(CURLOPT_PROXYPORT, $port);
        }
        if ($username !== null && $password !== null) {
            $this->setOpt(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
        }
    }

    /**
     * Set Proxies
     *
     * Set proxies to tunnel requests through. When set, a random proxy will be
     * used for the request.
     *
     * @access public
     * @param  $proxies array - A list of HTTP proxies to tunnel requests
     *     through. May include port number.
     */
    public function setProxies($proxies)
    {
        $this->proxies = $proxies;
    }

    /**
     * Set Proxy Auth
     *
     * Set the HTTP authentication method(s) to use for the proxy connection.
     *
     * @access public
     * @param  $auth
     */
    public function setProxyAuth($auth)
    {
        $this->setOpt(CURLOPT_PROXYAUTH, $auth);
    }

    /**
     * Set Proxy Type
     *
     * Set the proxy protocol type.
     *
     * @access public
     * @param  $type
     */
    public function setProxyType($type)
    {
        $this->setOpt(CURLOPT_PROXYTYPE, $type);
    }

    /**
     * Set Proxy Tunnel
     *
     * Set the proxy to tunnel through HTTP proxy.
     *
     * @access public
     * @param  $tunnel boolean
     */
    public function setProxyTunnel($tunnel = true)
    {
        $this->setOpt(CURLOPT_HTTPPROXYTUNNEL, $tunnel);
    }

    /**
     * Unset Proxy
     *
     * Disable use of the proxy.
     *
     * @access public
     */
    public function unsetProxy()
    {
        $this->setOpt(CURLOPT_PROXY, null);
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
     * Set Range
     *
     * @access public
     * @param  $range
     */
    public function setRange($range)
    {
        $this->setOpt(CURLOPT_RANGE, $range);
    }

    /**
     * Set Rate Limit
     *
     * @access public
     * @param  $rate_limit string (e.g. "60/1m").
     * @throws \UnexpectedValueException
     */
    public function setRateLimit($rate_limit)
    {
        $rate_limit_pattern =
            '/' .       // delimiter
            '^' .       // assert start
            '(\d+)' .   // digit(s)
            '\/' .      // slash
            '(\d+)?' .  // digit(s), optional
            '(s|m|h)' . // unit, s for seconds, m for minutes, h for hours
            '$' .       // assert end
            '/' .       // delimiter
            'i' .       // case-insensitive matches
            '';
        if (!preg_match($rate_limit_pattern, $rate_limit, $matches)) {
            throw new \UnexpectedValueException(
                'rate limit must be formatted as $max_requests/$interval(s|m|h) ' .
                '(e.g. "60/1m" for a maximum of 60 requests per 1 minute)'
            );
        }

        $max_requests = (int)$matches['1'];
        if ($matches['2'] === '') {
            $interval = 1;
        } else {
            $interval = (int)$matches['2'];
        }
        $unit = strtolower($matches['3']);

        // Convert interval to seconds based on unit.
        if ($unit === 's') {
            $interval_seconds = $interval * 1;
        } elseif ($unit === 'm') {
            $interval_seconds = $interval * 60;
        } elseif ($unit === 'h') {
            $interval_seconds = $interval * 3600;
        }

        $this->rateLimit = $max_requests . '/' . $interval . $unit;
        $this->rateLimitEnabled = true;
        $this->maxRequests = $max_requests;
        $this->interval = $interval;
        $this->intervalSeconds = $interval_seconds;
        $this->unit = $unit;
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
     * Number of retries to attempt or decider callable.
     *
     * When using a number of retries to attempt, the maximum number of attempts
     * for the request is $maximum_number_of_retries + 1.
     *
     * When using a callable decider, the request will be retried until the
     * function returns a value which evaluates to false.
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
     * Disable Timeout
     *
     * @access public
     */
    public function disableTimeout()
    {
        $this->setTimeout(null);
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
     * Set Interface
     *
     * The name of the outgoing network interface to use.
     * This can be an interface name, an IP address or a host name.
     *
     * @access public
     * @param  $interface
     */
    public function setInterface($interface)
    {
        $this->setOpt(CURLOPT_INTERFACE, $interface);
    }

    /**
     * Start
     *
     * @access public
     * @throws \ErrorException
     */
    public function start()
    {
        if ($this->isStarted) {
            return;
        }

        $this->isStarted = true;
        $this->currentStartTime = microtime(true);
        $this->currentRequestCount = 0;

        do {
            while (count($this->curls) &&
                count($this->activeCurls) < $this->concurrency &&
                (!$this->rateLimitEnabled || $this->hasRequestQuota())
            ) {
                $this->initHandle();
            }

            if ($this->rateLimitEnabled && !count($this->activeCurls) && !$this->hasRequestQuota()) {
                $this->waitUntilRequestQuotaAvailable();
            }

            // Wait for activity on any curl_multi connection when curl_multi_select (libcurl) fails to correctly block.
            // https://bugs.php.net/bug.php?id=63411
            if (curl_multi_select($this->multiCurl) === -1) {
                usleep(100000);
            }

            curl_multi_exec($this->multiCurl, $active);

            while (($info_array = curl_multi_info_read($this->multiCurl)) !== false) {
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
                                if ($curlm_error_code !== CURLM_OK) {
                                    throw new \ErrorException(
                                        'cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code)
                                    );
                                }
                            } else {
                                $curl->execDone();

                                // Remove completed instance from active curls.
                                unset($this->activeCurls[$key]);

                                // Remove handle of the completed instance.
                                curl_multi_remove_handle($this->multiCurl, $curl->curl);

                                // Clean up completed instance.
                                $curl->close();
                            }

                            break;
                        }
                    }
                }
            }
        } while ($active || count($this->activeCurls) || count($this->curls));

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
    private function initHandle()
    {
        $curl = array_shift($this->curls);
        if ($curl === null) {
            return;
        }

        // Add instance to list of active curls.
        $this->currentRequestCount += 1;
        $this->activeCurls[$curl->id] = $curl;

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

        // Use a random proxy for the curl instance when proxies have been set
        // and the curl instance doesn't already have a proxy set.
        if (is_array($this->proxies) && $curl->getOpt(CURLOPT_PROXY) === null) {
            $random_proxy = ArrayUtil::arrayRandom($this->proxies);
            $curl->setProxy($random_proxy);
        }

        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if ($curlm_error_code !== CURLM_OK) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }

        $curl->call($curl->beforeSendCallback);
    }

    /**
     * Has Request Quota
     *
     * Checks if there is any available quota to make additional requests while
     * rate limiting is enabled.
     *
     * @access private
     */
    private function hasRequestQuota()
    {
        // Calculate if there's request quota since ratelimiting is enabled.
        if ($this->rateLimitEnabled) {
            // Determine if the limit of requests per interval has been reached.
            if ($this->currentRequestCount >= $this->maxRequests) {
                $elapsed_seconds = microtime(true) - $this->currentStartTime;
                if ($elapsed_seconds <= $this->intervalSeconds) {
                    $this->rateLimitReached = true;
                    return false;
                } elseif ($this->rateLimitReached) {
                    $this->rateLimitReached = false;
                    $this->currentStartTime = microtime(true);
                    $this->currentRequestCount = 0;
                }
            }

            return true;
        } else {
            return true;
        }
    }

    /**
     * Wait Until Request Quota Available
     *
     * Waits until there is available request quota available based on the rate limit.
     *
     * @access private
     */
    private function waitUntilRequestQuotaAvailable()
    {
        $sleep_until = $this->currentStartTime + $this->intervalSeconds;
        $sleep_seconds = $sleep_until - microtime(true);

        // Avoid using time_sleep_until() as it appears to be less precise and not sleep long enough.
        usleep($sleep_seconds * 1000000);

        $this->currentStartTime = microtime(true);
        $this->currentRequestCount = 0;
    }
}
