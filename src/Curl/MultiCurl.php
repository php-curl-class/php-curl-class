<?php

namespace Curl;

class MultiCurl
{
    public $base_url = null;
    public $multi_curl;
    public $curls = array();
    private $curl_fhs = array();

    private $before_send_function = null;
    private $success_function = null;
    private $error_function = null;
    private $complete_function = null;

    private $cookies = array();
    private $headers = array();
    private $options = array();

    private $json_decoder = null;

    /**
     * Class constructor
     *
     * @access public
     * @param $base_url, bool 
     */
    public function __construct($base_url = null)
    {
        $this->multi_curl = curl_multi_init();
        $this->headers = new CaseInsensitiveArray();
        $this->setURL($base_url);
    }

    /**
     * add delete
     *
     * @access public
     * @param  $url
     * @param  $data, array
     *
     * @return $curl
     */
    public function addDelete($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
        }
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->addHandle($curl);
        return $curl;
    }

    /**
     * add download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return $curl
     */
    public function addDownload($url, $mixed_filename)
    {
        $curl = new Curl();
        $curl->setURL($url);

        if (is_callable($mixed_filename)) {
            $callback = $mixed_filename;
            $curl->download_complete_function = $callback;
            $fh = tmpfile();
        } else {
            $filename = $mixed_filename;
            $fh = fopen($filename, 'wb');
        }

        $curl->setOpt(CURLOPT_FILE, $fh);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->addHandle($curl);
        $this->curl_fhs[$curl->id] = $fh;
        return $curl;
    }

    /**
     * add get
     *
     * @access public
     * @param  $url
     * @param  $data, array
     *
     * @return $curl
     */
    public function addGet($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
        }
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->addHandle($curl);
        return $curl;
    }

    /**
     * add head
     *
     * @access public
     * @param  $url
     * @param  $data, array
     *
     * @return $curl
     */
    public function addHead($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
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
     * @param  $data, array
     *
     * @return $curl
     */
    public function addOptions($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
        }
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        $this->addHandle($curl);
        return $curl;
    }

    /**
     * Add patch
     *
     * @access public
     * @param  $url
     * @param  $data, array
     *
     * @return $curl
     */
    public function addPatch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
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
     * Add post
     *
     * @access public
     * @param  $url, string
     * @param  $data, Array
     *
     * @return
     */
    public function addPost($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
        }

        $curl = new Curl();

        if (is_array($data) && empty($data)) {
            $curl->unsetHeader('Content-Length');
        }

        $curl->setURL($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->addHandle($curl);
        return $curl;
    }

    /**
     * add Put
     *
     * @access public
     * @param  $url
     * @param  $data, array
     *
     * @return $curl
     */
    public function addPut($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->base_url;
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
     * before send
     *
     * @access public
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->before_send_function = $callback;
    }

    /**
     * Cloase
     *
     * @access public
     */
    public function close()
    {
        foreach ($this->curls as $ch) {
            $ch->close();
        }

        curl_multi_close($this->multi_curl);
    }

    /**
     * complete
     *
     * @access public
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->complete_function = $callback;
    }

    /**
     * error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback)
    {
        $this->error_function = $callback;
    }

    /**
     * getopt
     *
     * @access public
     * @param  $option
     */
    public function getOpt($option)
    {
        return $this->options[$option];
    }

    /**
     * Set basic authencation
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
     * Set cookie
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
     * set Cookie File
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
     * Set header
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
     * set JSON decoder
     *
     * @access public
     * @param  $function
     */
    public function setJsonDecoder($function)
    {
        if (is_callable($function)) {
            $this->json_decoder = $function;
        }
    }

    /**
     * setOpt
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
     * set Referer
     *
     * @access public
     * @param  $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }

    /**
     * set Referrer
     *
     * @access public
     * @param  $refferer
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
     * Set url
     *
     * @access public
     * @param  $url
     */
    public function setURL($url)
    {
        $this->base_url = $url;
    }

    /**
     * Set useragent
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
            foreach ($this->options as $option => $value) {
                $ch->setOpt($option, $value);
            }
            foreach ($this->headers as $key => $value) {
                $ch->setHeader($key, $value);
            }
            $ch->setJsonDecoder($this->json_decoder);
            $ch->call($ch->before_send_function);
        }

        $curl_handles = $this->curls;
        do {
            curl_multi_select($this->multi_curl);
            curl_multi_exec($this->multi_curl, $active);

            while (!($info_array = curl_multi_info_read($this->multi_curl)) === false) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    foreach ($curl_handles as $key => $ch) {
                        if ($ch->curl === $info_array['handle']) {
                            $ch->curl_error_code = $info_array['result'];
                            $ch->exec($ch->curl);
                            curl_multi_remove_handle($this->multi_curl, $ch->curl);
                            unset($curl_handles[$key]);

                            // Close open file handles and reset the curl instance.
                            if (isset($this->curl_fhs[$ch->id])) {
                                $ch->downloadComplete($this->curl_fhs[$ch->id]);
                                unset($this->curl_fhs[$ch->id]);
                            }
                            break;
                        }
                    }
                }
            }
        } while ($active > 0);
    }

    /**
     * success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback)
    {
        $this->success_function = $callback;
    }

    /**
     * Unset header
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
     * @param  $on, Boolean
     */
    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Add Handle
     *
     * @access private
     * @param  $curl
     */
    private function addHandle($curl)
    {
        $curlm_error_code = curl_multi_add_handle($this->multi_curl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }
        $curl->beforeSend($this->before_send_function);
        $curl->success($this->success_function);
        $curl->error($this->error_function);
        $curl->complete($this->complete_function);
        $this->curls[] = $curl;
        $curl->id = count($this->curls);
    }
}
