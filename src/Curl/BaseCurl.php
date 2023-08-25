<?php

declare(strict_types=1);

namespace Curl;

abstract class BaseCurl
{
    public $beforeSendCallback = null;
    public $afterSendCallback = null;
    public $successCallback = null;
    public $errorCallback = null;
    public $completeCallback = null;

    protected $options = [];
    protected $userSetOptions = [];

    /**
     * Before Send
     *
     * @param $callback callable|null
     */
    public function beforeSend($callback)
    {
        $this->beforeSendCallback = $callback;
    }

    abstract public function close();

    /**
     * Complete
     *
     * @param $callback callable|null
     */
    public function complete($callback)
    {
        $this->completeCallback = $callback;
    }

    /**
     * Disable Timeout
     */
    public function disableTimeout()
    {
        $this->setTimeout(null);
    }

    /**
     * Error
     *
     * @param $callback callable|null
     */
    public function error($callback)
    {
        $this->errorCallback = $callback;
    }

    /**
     * Get Opt
     *
     * @param        $option
     * @return mixed
     */
    public function getOpt($option)
    {
        return $this->options[$option] ?? null;
    }

    /**
     * Remove Header
     *
     * Remove an internal header from the request.
     * Using `curl -H "Host:" ...' is equivalent to $curl->removeHeader('Host');.
     *
     * @param $key
     */
    public function removeHeader($key)
    {
        $this->setHeader($key, '');
    }

    /**
     * Set auto referer
     *
     * @param mixed $auto_referer
     */
    public function setAutoReferer($auto_referer = true)
    {
        $this->setAutoReferrer($auto_referer);
    }

    /**
     * Set auto referrer
     *
     * @param mixed $auto_referrer
     */
    public function setAutoReferrer($auto_referrer = true)
    {
        $this->setOpt(CURLOPT_AUTOREFERER, $auto_referrer);
    }

    /**
     * Set Basic Authentication
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
     * Set Connect Timeout
     *
     * @param $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    abstract public function setCookie($key, $value);
    abstract public function setCookieFile($cookie_file);
    abstract public function setCookieJar($cookie_jar);
    abstract public function setCookieString($string);
    abstract public function setCookies($cookies);

    /**
     * Set Digest Authentication
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
     * After Send
     *
     * This function is called after the request has been sent.
     *
     * It can be used to override whether or not the request errored. The
     * instance is passed as the first argument to the function and the instance
     * has attributes like $instance->httpStatusCode and $instance->response to
     * help decide if the request errored. Set $instance->error to true or false
     * within the function.
     *
     * When $instance->error is true indicating a request error, the error
     * callback set by Curl::error() is called. When $instance->error is false,
     * the success callback set by Curl::success() is called.
     *
     * @param $callback callable|null
     */
    public function afterSend($callback)
    {
        $this->afterSendCallback = $callback;
    }

    /**
     * Set File
     *
     * @param $file
     */
    public function setFile($file)
    {
        $this->setOpt(CURLOPT_FILE, $file);
    }

    protected function setFileInternal($file)
    {
        $this->setOptInternal(CURLOPT_FILE, $file);
    }

    /**
     * Set follow location
     *
     * @param mixed $follow_location
     * @see    Curl::setMaximumRedirects()
     */
    public function setFollowLocation($follow_location = true)
    {
        $this->setOpt(CURLOPT_FOLLOWLOCATION, $follow_location);
    }

    /**
     * Set forbid reuse
     *
     * @param mixed $forbid_reuse
     */
    public function setForbidReuse($forbid_reuse = true)
    {
        $this->setOpt(CURLOPT_FORBID_REUSE, $forbid_reuse);
    }

    abstract public function setHeader($key, $value);
    abstract public function setHeaders($headers);

    /**
     * Set Interface
     *
     * The name of the outgoing network interface to use.
     * This can be an interface name, an IP address or a host name.
     *
     * @param $interface
     */
    public function setInterface($interface)
    {
        $this->setOpt(CURLOPT_INTERFACE, $interface);
    }

    abstract public function setJsonDecoder($mixed);

    /**
     * Set maximum redirects
     *
     * @param mixed $maximum_redirects
     * @see    Curl::setFollowLocation()
     */
    public function setMaximumRedirects($maximum_redirects)
    {
        $this->setOpt(CURLOPT_MAXREDIRS, $maximum_redirects);
    }

    abstract public function setOpt($option, $value);

    protected function setOptInternal($option, $value)
    {
    }

    abstract public function setOpts($options);

    /**
     * Set Port
     *
     * @param $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, (int) $port);
    }

    /**
     * Set Proxy
     *
     * Set an HTTP proxy to tunnel requests through.
     *
     * @param $proxy    - The HTTP proxy to tunnel requests through. May include port number.
     * @param $port     - The port number of the proxy to connect to. This port number can also be set in $proxy.
     * @param $username - The username to use for the connection to the proxy.
     * @param $password - The password to use for the connection to the proxy.
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
     * Set Proxy Auth
     *
     * Set the HTTP authentication method(s) to use for the proxy connection.
     *
     * @param $auth
     */
    public function setProxyAuth($auth)
    {
        $this->setOpt(CURLOPT_PROXYAUTH, $auth);
    }

    /**
     * Set Proxy Tunnel
     *
     * Set the proxy to tunnel through HTTP proxy.
     *
     * @param $tunnel boolean
     */
    public function setProxyTunnel($tunnel = true)
    {
        $this->setOpt(CURLOPT_HTTPPROXYTUNNEL, $tunnel);
    }

    /**
     * Set Proxy Type
     *
     * Set the proxy protocol type.
     *
     * @param $type
     */
    public function setProxyType($type)
    {
        $this->setOpt(CURLOPT_PROXYTYPE, $type);
    }

    /**
     * Set Range
     *
     * @param $range
     */
    public function setRange($range)
    {
        $this->setOpt(CURLOPT_RANGE, $range);
    }

    protected function setRangeInternal($range)
    {
        $this->setOptInternal(CURLOPT_RANGE, $range);
    }

    /**
     * Set Referer
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
     * @param $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    abstract public function setRetry($mixed);

    /**
     * Set Timeout
     *
     * @param $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    protected function setTimeoutInternal($seconds)
    {
        $this->setOptInternal(CURLOPT_TIMEOUT, $seconds);
    }

    abstract public function setUrl($url, $mixed_data = '');

    /**
     * Set User Agent
     *
     * @param $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    protected function setUserAgentInternal($user_agent)
    {
        $this->setOptInternal(CURLOPT_USERAGENT, $user_agent);
    }

    abstract public function setXmlDecoder($mixed);
    abstract public function stop();

    /**
     * Success
     *
     * @param $callback callable|null
     */
    public function success($callback)
    {
        $this->successCallback = $callback;
    }

    abstract public function unsetHeader($key);

    /**
     * Unset Proxy
     *
     * Disable use of the proxy.
     */
    public function unsetProxy()
    {
        $this->setOpt(CURLOPT_PROXY, null);
    }

    /**
     * Verbose
     *
     * @param bool            $on
     * @param resource|string $output
     */
    public function verbose($on = true, $output = 'STDERR')
    {
        if ($output === 'STDERR') {
            if (!defined('STDERR')) {
                define('STDERR', fopen('php://stderr', 'wb'));
            }
            $output = STDERR;
        }

        // Turn off CURLINFO_HEADER_OUT for verbose to work. This has the side
        // effect of causing Curl::requestHeaders to be empty.
        if ($on) {
            $this->setOpt(CURLINFO_HEADER_OUT, false);
        }
        $this->setOpt(CURLOPT_VERBOSE, $on);
        $this->setOpt(CURLOPT_STDERR, $output);
    }
}
