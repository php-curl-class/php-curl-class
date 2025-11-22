<?php

declare(strict_types=1);

namespace Curl;

class MultiCurl extends BaseCurl
{
    public $baseUrl = null;
    public $multiCurl = null;

    public $startTime = null;
    public $stopTime = null;

    private $queuedCurls = [];
    protected \WeakMap $activeCurls;
    private $isStarted = false;
    private $currentStartTime = null;
    private $currentRequestCount = 0;
    private $concurrency = 25;
    private $nextCurlId = 0;

    private $rateLimit = null;
    private $rateLimitEnabled = false;
    private $rateLimitReached = false;
    private $maxRequestsPerInterval = null;
    private $interval = null;
    private $intervalSeconds = null;
    private $unit = null;

    private $retry = null;

    private $cookies = [];
    private $headers = [];
    private $instanceSpecificOptions = [];
    private $proxies = null;

    private $jsonDecoder = null;
    private $xmlDecoder = null;

    /**
     * Construct
     *
     * @param $base_url
     */
    public function __construct($base_url = null)
    {
        $this->multiCurl = curl_multi_init();
        $this->headers = new CaseInsensitiveArray();
        $this->activeCurls = new \WeakMap();

        if ($base_url !== null) {
            $this->setUrl($base_url);
        }
    }

    /**
     * Add Delete
     *
     * @param         $url
     * @param         $query_parameters
     * @param         $data
     * @return object
     */
    public function addDelete($url, $query_parameters = [], $data = [])
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $url
     * @param         $mixed_filename
     * @return object
     */
    public function addDownload($url, $mixed_filename)
    {
        $curl = new Curl($this->baseUrl, $this->options);
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
                $range = (string)$first_byte_position . '-';
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
                    $contents = stream_get_contents($fh);
                    if ($contents !== false) {
                        file_put_contents($filename, $contents);
                    }
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
     * @param         $url
     * @param         $data
     * @return object
     */
    public function addGet($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $url
     * @param         $data
     * @return object
     */
    public function addHead($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $url
     * @param         $data
     * @return object
     */
    public function addOptions($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $url
     * @param         $data
     * @return object
     */
    public function addPatch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);

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
     * @param         $url
     * @param         $data
     * @param         $follow_303_with_post
     *                                     If true, will cause 303 redirections to be followed using a POST request
     *                                     (default: false). Note: Redirections are only followed if the
     *                                     CURLOPT_FOLLOWLOCATION option is set to true.
     * @return object
     */
    public function addPost($url, $data = '', $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool)$data;
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $url
     * @param         $data
     * @return object
     */
    public function addPut($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $url
     * @param         $data
     * @return object
     */
    public function addSearch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl($this->baseUrl, $this->options);
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
     * @param         $curl
     * @return object
     */
    public function addCurl(Curl $curl)
    {
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Close
     */
    #[\Override]
    public function close()
    {
        foreach ($this->queuedCurls as $curl) {
            $curl->close();
        }

        if (is_resource($this->multiCurl) || $this->multiCurl instanceof \CurlMultiHandle) {
            curl_multi_close($this->multiCurl);
        }
        $this->multiCurl = null;
    }

    /**
     * Set Concurrency
     *
     * @param $concurrency
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
    }

    /**
     * Set Cookie
     *
     * @param $key
     * @param $value
     */
    #[\Override]
    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
    }

    /**
     * Set Cookies
     *
     * @param $cookies
     */
    #[\Override]
    public function setCookies($cookies)
    {
        foreach ($cookies as $key => $value) {
            $this->cookies[$key] = $value;
        }
    }

    /**
     * Set Cookie String
     *
     * @param $string
     */
    #[\Override]
    public function setCookieString($string)
    {
        $this->setOpt(CURLOPT_COOKIE, $string);
    }

    /**
     * Set Cookie File
     *
     * @param $cookie_file
     */
    #[\Override]
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * Set Cookie Jar
     *
     * @param $cookie_jar
     */
    #[\Override]
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * Set Header
     *
     * Add extra header to include in the request.
     *
     * @param $key
     * @param $value
     */
    #[\Override]
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
     * @param $headers
     */
    #[\Override]
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
                list($key, $value) = array_pad(explode(':', $header, 2), 2, '');
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
     * @param $mixed boolean|callable
     */
    #[\Override]
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
     * @param $mixed boolean|callable
     */
    #[\Override]
    public function setXmlDecoder($mixed)
    {
        if ($mixed === false) {
            $this->xmlDecoder = false;
        } elseif (is_callable($mixed)) {
            $this->xmlDecoder = $mixed;
        }
    }

    /**
     * Set Proxies
     *
     * Set proxies to tunnel requests through. When set, a random proxy will be
     * used for the request.
     *
     * @param $proxies array - A list of HTTP proxies to tunnel requests
     *                 through. May include port number.
     */
    public function setProxies($proxies)
    {
        $this->proxies = $proxies;
    }

    /**
     * Set Opt
     *
     * @param $option
     * @param $value
     */
    #[\Override]
    public function setOpt($option, $value)
    {
        $this->options[$option] = $value;

        // Make changing the url an instance-specific option. Set the value of
        // existing instances when they have not already been set to avoid
        // unexpectedly changing the request url after is has been specified.
        if ($option === CURLOPT_URL) {
            foreach ($this->queuedCurls as $curl_id => $curl) {
                if (
                    !isset($this->instanceSpecificOptions[$curl_id][$option]) ||
                    $this->instanceSpecificOptions[$curl_id][$option] === null
                ) {
                    $this->instanceSpecificOptions[$curl_id][$option] = $value;
                }
            }
        }
    }

    /**
     * Set Opts
     *
     * @param $options
     */
    #[\Override]
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            $this->setOpt($option, $value);
        }
    }

    /**
     * Set Rate Limit
     *
     * @param                            $rate_limit string (e.g. "60/1m").
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
                'rate limit must be formatted as $max_requests_per_interval/$interval(s|m|h) ' .
                '(e.g. "60/1m" for a maximum of 60 requests per 1 minute)'
            );
        }

        $max_requests_per_interval = (int)$matches['1'];
        if ($matches['2'] === '') {
            $interval = 1;
        } else {
            $interval = (int)$matches['2'];
        }
        $unit = strtolower($matches['3']);

        // Convert interval to seconds based on unit.
        $interval_seconds = '';
        if ($unit === 's') {
            $interval_seconds = $interval * 1;
        } elseif ($unit === 'm') {
            $interval_seconds = $interval * 60;
        } elseif ($unit === 'h') {
            $interval_seconds = $interval * 3600;
        }

        $this->rateLimit = (string)$max_requests_per_interval . '/' . (string)$interval . $unit;
        $this->rateLimitEnabled = true;
        $this->maxRequestsPerInterval = $max_requests_per_interval;
        $this->interval = $interval;
        $this->intervalSeconds = $interval_seconds;
        $this->unit = $unit;
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
     * @param $mixed
     */
    #[\Override]
    public function setRetry($mixed)
    {
        $this->retry = $mixed;
    }

    /**
     * Set Url
     *
     * @param $url
     * @param $mixed_data
     */
    #[\Override]
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
     * Start
     *
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
            while (
                count($this->queuedCurls) &&
                count($this->activeCurls) < $this->concurrency &&
                (!$this->rateLimitEnabled || $this->hasRequestQuota())
            ) {
                $this->initHandle();
            }

            if ($this->rateLimitEnabled && !count($this->activeCurls) && !$this->hasRequestQuota()) {
                $this->waitUntilRequestQuotaAvailable();
            }

            curl_multi_exec($this->multiCurl, $active);

            while (
                (is_resource($this->multiCurl) || $this->multiCurl instanceof \CurlMultiHandle) &&
                (($info_array = curl_multi_info_read($this->multiCurl)) !== false)
            ) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    $native_handle = $info_array['handle'];

                    if ($this->activeCurls->offsetExists($native_handle)) {
                        $curl = $this->activeCurls[$native_handle];

                        // Set the error code for multi handles using the "result" key in the array returned by
                        // curl_multi_info_read(). Using curl_errno() on a multi handle will incorrectly return 0
                        // for errors.
                        $curl->curlErrorCode = $info_array['result'];
                        $curl->exec($native_handle);

                        if ($curl->attemptRetry()) {
                            // Remove completed handle before adding again in order to retry request.
                            curl_multi_remove_handle($this->multiCurl, $native_handle);

                            $curlm_error_code = curl_multi_add_handle($this->multiCurl, $native_handle);
                            if ($curlm_error_code !== CURLM_OK) {
                                throw new \ErrorException(
                                    'cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code)
                                );
                            }

                            $curl->call($curl->beforeSendCallback);
                        } else {
                            $curl->execDone();

                            // Remove completed instance from active curls.
                            $this->activeCurls->offsetUnset($native_handle);

                            // Remove handle of the completed instance.
                            curl_multi_remove_handle($this->multiCurl, $native_handle);

                            // Clean up completed instance.
                            $curl->close();
                        }
                    }
                }
            }
        } while ($active || count($this->activeCurls) || count($this->queuedCurls));

        $this->isStarted = false;
        $this->stopTime = microtime(true);
    }

    /**
     * Stop
     */
    #[\Override]
    public function stop()
    {
        if (!$this->isStarted) {
            return;
        }

        // Remove any queued curl requests.
        while (count($this->queuedCurls)) {
            $curl = array_pop($this->queuedCurls);
            $curl->close();
        }

        /**
         * @var \CurlHandle $native_handle
         * @var \Curl\Curl  $curl
         */
        // Attempt to stop active curl requests.
        foreach ($this->activeCurls as $native_handle => $curl) {
            // Remove active curl handle.
            curl_multi_remove_handle($this->multiCurl, $native_handle);

            // Remove instance from active curls.
            $this->activeCurls->offsetUnset($native_handle);

            $curl->stop();
        }

        $this->isStarted = false;
        $this->stopTime = microtime(true);
    }

    /**
     * Unset Header
     *
     * Remove extra header previously set using Curl::setHeader().
     *
     * @param $key
     */
    #[\Override]
    public function unsetHeader($key)
    {
        unset($this->headers[$key]);
    }

    /**
     * Set request time accuracy
     *
     * @deprecated This method is deprecated and no longer has any effect.
     */
    public function setRequestTimeAccuracy()
    {
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Update Headers
     */
    private function updateHeaders()
    {
        foreach ($this->queuedCurls as $curl) {
            $curl->setHeaders($this->headers);
        }
    }

    /**
     * Queue Handle
     *
     * @param $curl
     */
    private function queueHandle($curl)
    {
        // Use sequential ids to allow for ordered post processing.
        $curl->id = $this->nextCurlId++;
        $curl->childOfMultiCurl = true;
        $this->queuedCurls[$curl->id] = $curl;

        // Avoid overwriting any existing header.
        if ($curl->getOpt(CURLOPT_HTTPHEADER) === null) {
            $curl->setHeaders($this->headers);
        }
    }

    /**
     * Init Handle
     *
     * @param                  $curl
     * @throws \ErrorException
     */
    private function initHandle()
    {
        $curl = array_shift($this->queuedCurls);
        if ($curl === null) {
            return;
        }

        // Add instance to list of active curls.
        $this->currentRequestCount += 1;
        $this->activeCurls[$curl->curl] = $curl;

        // Set callbacks if not already individually set.
        if ($curl->beforeSendCallback === null) {
            $curl->beforeSend($this->beforeSendCallback);
        }
        if ($curl->afterSendCallback === null) {
            $curl->afterSend($this->afterSendCallback);
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
     */
    private function hasRequestQuota()
    {
        // Calculate if there's request quota since ratelimiting is enabled.
        if ($this->rateLimitEnabled) {
            // Determine if the limit of requests per interval has been reached.
            if ($this->currentRequestCount >= $this->maxRequestsPerInterval) {
                $micro_time = microtime(true);
                $elapsed_seconds = $micro_time - $this->currentStartTime;
                if ($elapsed_seconds <= $this->intervalSeconds) {
                    // Rate limit reached.
                    return false;
                } else {
                    // Rate limit not reached. Rate limit interval has passed,
                    // reset counters.
                    $this->currentStartTime = $micro_time;
                    $this->currentRequestCount = 0;
                }
            }
        }

        return true;
    }

    /**
     * Wait Until Request Quota Available
     *
     * Waits until there is available request quota available based on the rate limit.
     */
    private function waitUntilRequestQuotaAvailable()
    {
        $sleep_until = TimeUtil::getSleepUntilMicrotime(
            $this->currentStartTime,
            $this->intervalSeconds,
        );

        $current_microtime = microtime(true);
        $sleep_seconds = TimeUtil::getSleepSecondsUntilMicrotime(
            $sleep_until,
            $current_microtime,
        );

        list($whole_seconds, $microseconds_remainder) = TimeUtil::getWholeAndRemainderSeconds($sleep_seconds);

        if ($whole_seconds >= 1) {
            sleep($whole_seconds);
        }

        if ($microseconds_remainder > 0) {
            usleep($microseconds_remainder);
        }

        $this->currentStartTime = microtime(true);
        $this->currentRequestCount = 0;
    }

    public function getActiveCurls()
    {
        return $this->activeCurls;
    }
}
