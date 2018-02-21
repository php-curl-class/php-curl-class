<?php

use Curl\MultiCurl;

class MultiCurlNoLatency extends MultiCurl
{
    /**
     * @inheritdoc
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
        array_push($args, false);

        $curl = $this->createCurl();
        $this->queueHandle($curl);
        call_user_func_array(array($curl, $method), $args);

        if ($this->concurrency > count($this->activeCurls)) {
            $this->initHandle(array_shift($this->curls));
        }
        $this->start();

        return $curl;
    }

    /**
     * @inheritdoc
     */
    protected function execMultiCurl()
    {
        curl_multi_exec($this->multiCurl, $this->active);

        $info_array = curl_multi_info_read($this->multiCurl);
        if ($info_array && $info_array['msg'] === CURLMSG_DONE) {
            $this->execCurlHandle($info_array['handle'], $info_array['result']);
        }

        if (!$this->active) {
            $this->active = count($this->activeCurls);
        }
    }

    /**
     * @throws ErrorException
     */
    public function wait()
    {
        while ($this->getActiveCount() > 0) {
            $this->start();
        };
    }

    /**
     * @throws ErrorException
     */
    public function close()
    {
        $this->wait();
        parent::close();
    }
}