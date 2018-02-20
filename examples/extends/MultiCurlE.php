<?php

use Curl\Curl;
use Curl\MultiCurl;

class MultiCurlE extends MultiCurl
{

    /**
     * @return CurlE
     * @throws ErrorException
     */
    protected function createCurl()
    {
        $curl = new CurlE();
        return $curl;
    }

    /**
     * Init Handle
     *
     * @access private
     *
     * @param  CurlE $curl
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

        if (!$curl->isInited()) {
            $curl->init();
        }

        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }

        $this->activeCurls[$curl->id] = $curl;
        $curl->call($curl->beforeSendCallback);
    }
}
