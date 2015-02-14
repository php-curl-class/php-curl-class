<?php

namespace Curl;

class MultiCurl
{
    public $multi_curl;
    public $curls = array();
    private $curl_fhs = array();

    private $headers = array();
    private $options = array();

    private $before_send_function = null;
    private $success_function = null;
    private $error_function = null;
    private $complete_function = null;

    public function __construct()
    {
        $this->multi_curl = curl_multi_init();
        $this->headers = new CaseInsensitiveArray();
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
        $curl = new Curl();
        $curl->setURL($url);
        $fh = fopen($filename, 'wb');
        $curl->setOpt(CURLOPT_FILE, $fh);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->addHandle($curl);
        $this->curl_fhs[$curl->id] = $fh;
        return $curl;
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
        $curl = new Curl();
        $curl->setURL($url, $data);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        $this->addHandle($curl);
        return $curl;
    }

    public function addPatch($url, $data = array())
    {
        $curl = new Curl();
        $curl->setURL($url);
        $curl->unsetHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $curl->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->addHandle($curl);
        return $curl;
    }

    public function addPost($url, $data = array())
    {
        $curl = new Curl();

        if (is_array($data) && empty($data)) {
            $curl->unsetHeader('Content-Length');
        }

        $curl->setURL($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->postfields($data));
        $this->addHandle($curl);
        return $curl;
    }

    public function addPut($url, $data = array())
    {
        $curl = new Curl();
        $curl->setURL($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $curl->postfields($data);
        $curl->setHeader('Content-Length', strlen($put_data));
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        $this->addHandle($curl);
        return $curl;
    }

    public function beforeSend($callback)
    {
        $this->before_send_function = $callback;
    }

    public function close()
    {
        foreach ($this->curls as $ch) {
            $ch->close();
        }

        curl_multi_close($this->multi_curl);
    }

    public function complete($callback)
    {
        $this->complete_function = $callback;
    }

    public function error($callback)
    {
        $this->error_function = $callback;
    }

    public function getOpt($option)
    {
        return $this->options[$option];
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function setOpt($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function start()
    {
        foreach ($this->curls as $ch) {
            foreach ($this->options as $option => $value) {
                $ch->setOpt($option, $value);
            }
            foreach ($this->headers as $key => $value) {
                $ch->setHeader($key, $value);
            }
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
                                fclose($this->curl_fhs[$ch->id]);
                                defined('STDOUT') || define('STDOUT', null);
                                $ch->setOpt(CURLOPT_FILE, STDOUT);
                                $ch->setOpt(CURLOPT_RETURNTRANSFER, true);
                                unset($this->curl_fhs[$ch->id]);
                            }
                            break;
                        }
                    }
                }
            }
        } while ($active > 0);
    }

    public function success($callback)
    {
        $this->success_function = $callback;
    }

    public function __destruct()
    {
        $this->close();
    }

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
