<?php

namespace Curl;

class MultiCurl
{
    public $curl_multi;
    public $curls = array();

    private $before_send_function = null;
    private $success_function = null;
    private $error_function = null;
    private $complete_function = null;

    public function __construct()
    {
        $this->curl_multi = curl_multi_init();
    }

    public function addDelete($url, $data = array())
    {
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->addHandle($curl);
        return $curl;
    }

    public function addDownload($url, $filename)
    {
    }

    public function addGet($url, $data = array())
    {
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->addHandle($curl);
        return $curl;
    }

    public function addHead($url, $data = array())
    {
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $curl->setOpt(CURLOPT_NOBODY, true);
        $this->addHandle($curl);
        return $curl;
    }

    public function addOptions($url, $data = array())
    {
    }

    public function addPatch($url, $data = array())
    {
    }

    public function addPost($url, $data = array())
    {
    }

    public function addPut($url, $data = array())
    {
    }

    public function beforeSend($callback)
    {
        echo 'setting multicurl beforeSend' . "\n";
        $this->before_send_function = $callback;
    }

    public function success($callback)
    {
        echo 'setting multicurl success' . "\n";
        $this->success_function = $callback;
    }

    public function error($callback)
    {
        echo 'setting multicurl error' . "\n";
        $this->error_function = $callback;
    }

    public function complete($callback)
    {
        echo 'setting multicurl complete' . "\n";
        $this->complete_function = $callback;
    }

    public function start()
    {
        echo 'running start' . "\n";
        foreach ($this->curls as $ch) {
            $ch->call($ch->before_send_function);
        }

        $curl_handles = $this->curls;
        do {
            echo str_repeat('-', 80) . "\n";
            curl_multi_select($this->curl_multi);
            curl_multi_exec($this->curl_multi, $active);
            $info_array = curl_multi_info_read($this->curl_multi);
            if (!($info_array === false)) {
              foreach ($curl_handles as $key => $ch) {
                  if ($ch->curl === $info_array['handle']) {
                      echo $ch->id . ' completed' . "\n";
                      $ch->curl_error_code = $info_array['result'];
                      $ch->exec($ch->curl);
                      curl_multi_remove_handle($this->curl_multi, $ch->curl);
                      unset($curl_handles[$key]);
                      break;
                  }
              }
            }
        } while ($active > 0);
    }

    public function close()
    {
        foreach ($this->curls as $ch) {
            $ch->close();
        }

        curl_multi_close($this->curl_multi);
    }

    public function __destruct()
    {
        $this->close();
    }

    private function addHandle($curl)
    {
        echo 'adding handle' . "\n";
        $curlm_error_code = curl_multi_add_handle($this->curl_multi, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }
        echo 'copying over callbacks from multicurl to curl' . "\n";
        $curl->beforeSend($this->before_send_function);
        $curl->success($this->success_function);
        $curl->error($this->error_function);
        $curl->complete($this->complete_function);
        $this->curls[] = $curl;
        $curl->id = count($this->curls);
        echo 'handle added' . "\n";
    }
}
