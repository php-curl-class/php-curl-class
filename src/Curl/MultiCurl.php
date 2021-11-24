<?php declare(strict_types=1);

namespace Curl;

use Curl\ArrayUtil;
use Curl\Url;

class MultiCurl
{
    public $baseUrl = null;
    public $multiCurl = null;

    public $startTime = null;
    public $stopTime = null;

    private $curls = [];
    private $activeCurls = [];
    private $isStarted = false;
    private $currentStartTime = null;
    private $currentRequestCount = 0;
    private $concurrency = 25;
    private $nextCurlId = 0;

    private $rateLimit = null;
    private $rateLimitEnabled = false;
    private $rateLimitReached = false;
    private $maxRequests = null;
    private $interval = null;
    private $intervalSeconds = null;
    private $unit = null;

    private $beforeSendCallback = null;
    private $successCallback = null;
    private $errorCallback = null;
    private $completeCallback = null;

    private $retry = null;

    private $cookies = [];
    private $headers = [];
    private $options = [];
    private $instanceSpecificOptions = [];
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

        if ($base_url !== null) {
            $this->setUrl($base_url);
        }
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
    public function addDelete($url, $query_parameters = [], $data = [])
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url, $query_parameters);
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
        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url);
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
            $curl->downloadFileName = $download_filename;

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
    public function addGet($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url, $data);
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
    public function addHead($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url, $data);
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
    public function addOptions($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url, $data);
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
    public function addPatch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);

        if (is_array($data) && empty($data)) {
            $curl->removeHeader('Content-Length');
        }

        $this->queueHandle($curl);
        $this->setUrl($url);
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
     *     If true, will cause 303 redirections to be followed using a POST request (default: false).
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

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url);

        if (is_array($data) && empty($data)) {
            $curl->removeHeader('Content-Length');
        }

        $curl->setUrl($url);

        // Set the request method to "POST" when following a 303 redirect with
        // an additional POST request is desired. This is equivalent to setting
        // the -X, --request command line option where curl won't change the
        // request method according to the HTTP 30x response code.
        if ($follow_303_with_post) {
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
    public function addPut($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url);
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
    public function addSearch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl);
        $this->queueHandle($curl);
        $this->setUrl($url);
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
     * @param  $callback callable|null
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

        if (is_resource($this->multiCurl) || $this->multiCurl instanceof \CurlMultiHandle) {
            curl_multi_close($this->multiCurl);
        }
        $this->multiCurl = null;
    }

    /**
     * Complete
     *
     * @access public
     * @param  $callback callable|null
     */
    public function complete($callback)
    {
        $this->completeCallback = $callback;
    }

    /**
     * Error
     *
     * @access public
     * @param  $callback callable|null
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
        $this->setOpt(CURLOPT_PORT, (int) $port);
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

        // Make changing the url an instance-specific option. Set the value of
        // existing instances when they have not already been set to avoid
        // unexpectedly changing the request url after is has been specified.
        if ($option === CURLOPT_URL) {
            foreach ($this->curls as $curl_id => $curl) {
                if (!isset($this->instanceSpecificOptions[$curl_id][$option]) ||
                    $this->instanceSpecificOptions[$curl_id][$option] === null) {
                    $this->instanceSpecificOptions[$curl_id][$option] = $value;
                }
            }
        }
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
     * @param  $mixed_data
     */
    public function setUrl($url, $mixed_data = '')
    {
        $built_url = Url::buildUrl($url, $mixed_data);

        if ($this->baseUrl === null) {
            $this->baseUrl = (string)new Url($built_url);
        } else {
            $this->baseUrl = (string)new Url($this->baseUrl, $built_url);
        }

        $this->setOpt(CURLOPT_URL, $this->baseUrl);
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
        $this->startTime = microtime(true);
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
            //
            // Also, use a shorter curl_multi_select() timeout instead the default of one second. This allows pending
            // requests to have more accurate start times. Without a shorter timeout, it can be nearly a full second
            // before available request quota is rechecked and pending requests can be initialized.
            if (curl_multi_select($this->multiCurl, 0.2) === -1) {
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
        $this->stopTime = microtime(true);
    }

    /**
     * Success
     *
     * @access public
     * @param  $callback callable|null
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
     * Set auto referer
     *
     * @access public
     */
    public function setAutoReferer($auto_referer = true)
    {
        $this->setAutoReferrer($auto_referer);
    }

    /**
     * Set auto referrer
     *
     * @access public
     */
    public function setAutoReferrer($auto_referrer = true)
    {
        $this->setOpt(CURLOPT_AUTOREFERER, $auto_referrer);
    }

    /**
     * Set follow location
     *
     * @access public
     */
    public function setFollowLocation($follow_location = true)
    {
        $this->setOpt(CURLOPT_FOLLOWLOCATION, $follow_location);
    }

    /**
     * Set forbid reuse
     *
     * @access public
     */
    public function setForbidReuse($forbid_reuse = true)
    {
        $this->setOpt(CURLOPT_FORBID_REUSE, $forbid_reuse);
    }

    /**
     * Set maximum redirects
     *
     * @access public
     */
    public function setMaximumRedirects($maximum_redirects)
    {
        $this->setOpt(CURLOPT_MAXREDIRS, $maximum_redirects);
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

        // Pass options set on the MultiCurl instance to the Curl instance.
        $curl->setOpts($this->options);

        // Set instance-specific options on the Curl instance when present.
        if (isset($this->instanceSpecificOptions[$curl->id])) {
            $curl->setOpts($this->instanceSpecificOptions[$curl->id]);
        }

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
                $micro_time = microtime(true);
                $elapsed_seconds = $micro_time - $this->currentStartTime;
                if ($elapsed_seconds <= $this->intervalSeconds) {
                    $this->rateLimitReached = true;
                    return false;
                } elseif ($this->rateLimitReached) {
                    $this->rateLimitReached = false;
                    $this->currentStartTime = $micro_time;
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
        usleep((int) $sleep_seconds * 1000000);

        // Ensure that enough time has passed as usleep() may not have waited long enough.
        $this->currentStartTime = microtime(true);
        if ($this->currentStartTime < $sleep_until) {
            do {
                usleep(1000000 / 4);
                $this->currentStartTime = microtime(true);
            } while ($this->currentStartTime < $sleep_until);
        }

        $this->currentRequestCount = 0;
    }
}
