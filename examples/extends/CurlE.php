<?php

use Curl\CaseInsensitiveArray;
use Curl\Curl;

class CurlE extends Curl
{
    private $inited = false;

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

        //$this->curl = curl_init();
        $this->id = uniqid('', true);
        $this->setDefaultUserAgent();
        $this->setDefaultTimeout();
        $this->setOpt(CURLINFO_HEADER_OUT, true);

        // Create a placeholder to temporarily store the header callback data.
        $header_callback_data = new \stdClass();
        $header_callback_data->rawResponseHeaders = '';
        $header_callback_data->responseCookies = array();
        $this->headerCallbackData = $header_callback_data;
        $this->setOpt(CURLOPT_HEADERFUNCTION, $this->createHeaderCallback($header_callback_data));

        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->headers = new CaseInsensitiveArray();
        $this->setUrl($base_url);
    }

    /**
     * @return bool
     */
    public function init()
    {
        $this->curl = curl_init();
        $success = curl_setopt_array($this->curl, $this->options);
        $this->inited = $success;

        return $this->inited;
    }

    /**
     * @return bool
     */
    public function isInited()
    {
        return $this->inited;
    }

    /**
     * Exec
     *
     * @access public
     * @param  $ch
     *
     * @return mixed Returns the value provided by parseResponse.
     */
    public function exec($ch = null)
    {
        if (!$this->isInited()) {
            $this->init();
        }

        return parent::exec($ch);
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
        $required_options = array(
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->options[$option] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            $this->setOpt($option, $value);
        }
    }

}
