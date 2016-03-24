<?php

namespace Curl;

class MultiCurl
{
    public $baseUrl = null;
    public $multiCurl;
    public $curls = array();
    private $curlFileHandles = array();
    private $nextCurlId = 1;
    private $isStarted = false;

    private $beforeSendFunction = null;
    private $successFunction = null;
    private $errorFunction = null;
    private $completeFunction = null;

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
        $this->setURL($base_url);
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
        $curl->setURL($url, $query_parameters);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->addHandle($curl);
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
        $curl->setURL($url);

        if (is_callable($mixed_filename)) {
            $callback = $mixed_filename;
            $curl->downloadCompleteFunction = $callback;
            $fh = tmpfile();
        } else {
            $filename = $mixed_filename;
            $fh = fopen($filename, 'wb');
        }

        $curl->setOpt(CURLOPT_FILE, $fh);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->addHandle($curl);
        $this->curlFileHandles[$curl->id] = $fh;
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
        $curl->setURL($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->addHandle($curl);
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
        $curl->setURL($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $curl->setOpt(CURLOPT_NOBODY, true);
        $this->addHandle($curl);
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
        $curl->setURL($url, $data);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        $this->addHandle($curl);
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
        $curl->setURL($url);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $curl->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->addHandle($curl);
        return $curl;
    }

    /**
     * Add Post
     *
     * @access public
     * @param  $url
     * @param  $data
     * @param  $post_redirect_get If true, will cause 303 redirections to be followed using
     *     GET requests (default: false).
     *     Note: Redirections are only followed if the CURLOPT_FOLLOWLOCATION option is set to true.
     *
     * @return object
     */
    public function addPost($url, $data = array(), $post_redirect_get = false)
    {
        if (is_array($url)) {
            $post_redirect_get = (bool)$data;
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl();

        if (is_array($data) && empty($data)) {
            $curl->unsetHeader('Content-Length');
        }

        $curl->setURL($url);

        /*
         * For post-redirect-get requests, the CURLOPT_CUSTOMREQUEST option must not
         * be set, otherwise cURL will perform POST requests for redirections.
         */
        if (!$post_redirect_get) {
            $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        }

        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->addHandle($curl);
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
        $curl->setURL($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $curl->buildPostData($data);
        $curl->setHeader('Content-Length', strlen($put_data));
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        $this->addHandle($curl);
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
        $this->beforeSendFunction = $callback;
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        foreach ($this->curls as $ch) {
            $ch->close();
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
        $this->completeFunction = $callback;
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
        $this->cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, str_replace('+', '%20', http_build_query($this->cookies, '', '; ')));
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
     * @access public
     * @param  $key
     * @param  $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
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
     * Set XML Decoder
     *
     * @access public
     * @param  $function
     */
    public function setXmlDecoder($function)
    {
        if (is_callable($function)) {
            $this->xmlDecoder = $function;
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
     */
    public function setURL($url)
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
        foreach ($this->curls as $ch) {
            $this->initHandle($ch);
        }

        $this->isStarted = true;

        do {
            curl_multi_select($this->multiCurl);
            curl_multi_exec($this->multiCurl, $active);

            while (!($info_array = curl_multi_info_read($this->multiCurl)) === false) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    foreach ($this->curls as $key => $ch) {
                        if ($ch->curl === $info_array['handle']) {
                            $ch->curlErrorCode = $info_array['result'];
                            $ch->exec($ch->curl);
                            curl_multi_remove_handle($this->multiCurl, $ch->curl);
                            unset($this->curls[$key]);

                            // Close open file handles and reset the curl instance.
                            if (isset($this->curlFileHandles[$ch->id])) {
                                $ch->downloadComplete($this->curlFileHandles[$ch->id]);
                                unset($this->curlFileHandles[$ch->id]);
                            }
                            break;
                        }
                    }
                }
            }

            if (!$active) {
                $active = count($this->curls);
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
    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
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
     * Add Handle
     *
     * @access private
     * @param  $curl
     * @throws \ErrorException
     */
    private function addHandle($curl)
    {
        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }
        $this->curls[] = $curl;
        $curl->id = $this->nextCurlId++;

        if ($this->isStarted) {
            $this->initHandle($curl);
        }
    }

    /**
     * Init Handle
     *
     * @access private
     * @param  $curl
     */
    private function initHandle($curl)
    {
        // Set callbacks if not already individually set.
        if ($curl->beforeSendFunction === null) {
            $curl->beforeSend($this->beforeSendFunction);
        }
        if ($curl->successFunction === null) {
            $curl->success($this->successFunction);
        }
        if ($curl->errorFunction === null) {
            $curl->error($this->errorFunction);
        }
        if ($curl->completeFunction === null) {
            $curl->complete($this->completeFunction);
        }

        foreach ($this->options as $option => $value) {
            $curl->setOpt($option, $value);
        }
        foreach ($this->headers as $key => $value) {
            $curl->setHeader($key, $value);
        }
        $curl->setJsonDecoder($this->jsonDecoder);
        $curl->setXmlDecoder($this->xmlDecoder);
        $curl->call($curl->beforeSendFunction);
    }
}
