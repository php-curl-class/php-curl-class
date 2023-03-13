<?php declare(strict_types=1);

namespace Curl;

use Curl\ArrayUtil;
use Curl\BaseCurl;
use Curl\Url;

class Curl extends BaseCurl
{
    const VERSION = '9.14.3';
    const DEFAULT_TIMEOUT = 30;

    public $curl = null;
    public $id = null;

    public $error = false;
    public $errorCode = 0;
    public $errorMessage = null;

    public $curlError = false;
    public $curlErrorCode = 0;
    public $curlErrorMessage = null;

    public $httpError = false;
    public $httpStatusCode = 0;
    public $httpErrorMessage = null;

    public $url = null;
    public $requestHeaders = [];

    public $responseHeaders = null;
    public $rawResponseHeaders = '';
    public $responseCookies = [];
    public $response = null;
    public $rawResponse = null;

    public $downloadCompleteCallback = null;
    public $fileHandle = null;
    public $downloadFileName = null;

    public $attempts = 0;
    public $retries = 0;
    public $childOfMultiCurl = false;
    public $remainingRetries = 0;
    public $retryDecider = null;

    public $jsonDecoder = null;
    public $xmlDecoder = null;

    private $headerCallbackData;
    private $cookies = [];
    private $headers = [];

    private $jsonDecoderArgs = [];
    private $jsonPattern = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';
    private $xmlDecoderArgs = [];
    private $xmlPattern = '~^(?:text/|application/(?:atom\+|rss\+|soap\+)?)xml~i';
    private $defaultDecoder = null;

    public static $RFC2616 = [
        // RFC 2616: "any CHAR except CTLs or separators".
        // CHAR           = <any US-ASCII character (octets 0 - 127)>
        // CTL            = <any US-ASCII control character
        //                  (octets 0 - 31) and DEL (127)>
        // separators     = "(" | ")" | "<" | ">" | "@"
        //                | "," | ";" | ":" | "\" | <">
        //                | "/" | "[" | "]" | "?" | "="
        //                | "{" | "}" | SP | HT
        // SP             = <US-ASCII SP, space (32)>
        // HT             = <US-ASCII HT, horizontal-tab (9)>
        // <">            = <US-ASCII double-quote mark (34)>
        '!', '#', '$', '%', '&', "'", '*', '+', '-', '.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B',
        'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q',
        'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '|', '~',
    ];
    public static $RFC6265 = [
        // RFC 6265: "US-ASCII characters excluding CTLs, whitespace DQUOTE, comma, semicolon, and backslash".
        // %x21
        '!',
        // %x23-2B
        '#', '$', '%', '&', "'", '(', ')', '*', '+',
        // %x2D-3A
        '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':',
        // %x3C-5B
        '<', '=', '>', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
        'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[',
        // %x5D-7E
        ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~',
    ];

    public $curlErrorCodeConstant;
    public $curlErrorCodeConstants;
    public $curlOptionCodeConstants;
    public $effectiveUrl;
    public $rfc2616;
    public $rfc6265;
    public $totalTime;

    private static $deferredProperties = [
        'curlErrorCodeConstant',
        'curlErrorCodeConstants',
        'curlOptionCodeConstants',
        'effectiveUrl',
        'rfc2616',
        'rfc6265',
        'totalTime',
    ];

    /**
     * Construct
     *
     * @access public
     * @param  $base_url
     * @throws \ErrorException
     */
    public function __construct($base_url = null, $options = [])
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        unset($this->curlErrorCodeConstant);
        unset($this->curlErrorCodeConstants);
        unset($this->curlOptionCodeConstants);
        unset($this->effectiveUrl);
        unset($this->rfc2616);
        unset($this->rfc6265);
        unset($this->totalTime);

        $this->curl = curl_init();
        $this->initialize($base_url, $options);
    }

    /**
     * Build Post Data
     *
     * @access public
     * @param  $data
     *
     * @return array|string
     * @throws \ErrorException
     */
    public function buildPostData($data)
    {
        $binary_data = false;

        // Return JSON-encoded string when the request's content-type is JSON and the data is serializable.
        if (isset($this->headers['Content-Type']) &&
            preg_match($this->jsonPattern, $this->headers['Content-Type']) &&
            (
                is_array($data) ||
                (
                    is_object($data) &&
                    interface_exists('JsonSerializable', false) &&
                    $data instanceof \JsonSerializable
                )
            )) {
            $data = \Curl\Encoder::encodeJson($data);
        } elseif (is_array($data)) {
            // Manually build a single-dimensional array from a multi-dimensional array as using curl_setopt($ch,
            // CURLOPT_POSTFIELDS, $data) doesn't correctly handle multi-dimensional arrays when files are
            // referenced.
            if (ArrayUtil::isArrayMultidim($data)) {
                $data = ArrayUtil::arrayFlattenMultidim($data);
            }

            // Modify array values to ensure any referenced files are properly handled depending on the support of
            // the @filename API or CURLFile usage. This also fixes the warning "curl_setopt(): The usage of the
            // @filename API for file uploading is deprecated. Please use the CURLFile class instead". Ignore
            // non-file values prefixed with the @ character.
            foreach ($data as $key => $value) {
                if (is_string($value) && strpos($value, '@') === 0 && is_file(substr($value, 1))) {
                    $binary_data = true;
                    if (class_exists('CURLFile')) {
                        $data[$key] = new \CURLFile(substr($value, 1));
                    }
                } elseif ($value instanceof \CURLFile) {
                    $binary_data = true;
                } elseif ($value instanceof \CURLStringFile) {
                    $binary_data = true;
                }
            }
        }

        if (!$binary_data &&
            (is_array($data) || is_object($data)) &&
            (
                !isset($this->headers['Content-Type']) ||
                !preg_match('/^multipart\/form-data/', $this->headers['Content-Type'])
            )) {
            // Avoid using http_build_query() as keys with null values are
            // unexpectedly excluded from the resulting string.
            //
            // $ php -a
            // php > echo http_build_query(['a' => '1', 'b' => null, 'c' => '3']);
            // a=1&c=3
            // php > echo http_build_query(['a' => '1', 'b' => '',   'c' => '3']);
            // a=1&b=&c=3
            //
            // $data = http_build_query($data, '', '&');
            $data = implode('&', array_map(function ($k, $v) {
                // Encode keys and values using urlencode() to match the default
                // behavior http_build_query() where $encoding_type is
                // PHP_QUERY_RFC1738.
                //
                // Use strval() as urlencode() expects a string parameter:
                //   TypeError: urlencode() expects parameter 1 to be string, integer given
                //   TypeError: urlencode() expects parameter 1 to be string, null given
                //
                // php_raw_url_encode()
                // php_url_encode()
                // https://github.com/php/php-src/blob/master/ext/standard/http.c
                return urlencode(strval($k)) . '=' . urlencode(strval($v));
            }, array_keys((array)$data), array_values((array)$data)));
        }

        return $data;
    }

    /**
     * Call
     *
     * @access public
     */
    public function call()
    {
        $args = func_get_args();
        $function = array_shift($args);
        if (is_callable($function)) {
            array_unshift($args, $this);
            call_user_func_array($function, $args);
        }
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        if (is_resource($this->curl) || $this->curl instanceof \CurlHandle) {
            curl_close($this->curl);
        }
        $this->curl = null;
        $this->options = null;
        $this->jsonDecoder = null;
        $this->jsonDecoderArgs = null;
        $this->xmlDecoder = null;
        $this->xmlDecoderArgs = null;
        $this->headerCallbackData = null;
        $this->defaultDecoder = null;
    }

    /**
     * Progress
     *
     * @access public
     * @param  $callback callable|null
     */
    public function progress($callback)
    {
        $this->setOpt(CURLOPT_PROGRESSFUNCTION, $callback);
        $this->setOpt(CURLOPT_NOPROGRESS, false);
    }

    /**
     * Delete
     *
     * @access public
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function delete($url, $query_parameters = [], $data = [])
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = (string)$this->url;
        }

        $this->setUrl($url, $query_parameters);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');

        // Avoid including a content-length header in DELETE requests unless there is a message body. The following
        // would include "Content-Length: 0" in the request header:
        //   curl_setopt($ch, CURLOPT_POSTFIELDS, []);
        // RFC 2616 4.3 Message Body:
        //   The presence of a message-body in a request is signaled by the
        //   inclusion of a Content-Length or Transfer-Encoding header field in
        //   the request's message-headers.
        if (!empty($data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        }
        return $this->exec();
    }

    /**
     * Download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return boolean
     */
    public function download($url, $mixed_filename)
    {
        // Use tmpfile() or php://temp to avoid "Too many open files" error.
        if (is_callable($mixed_filename)) {
            $this->downloadCompleteCallback = $mixed_filename;
            $this->downloadFileName = null;
            $this->fileHandle = tmpfile();
        } else {
            $filename = $mixed_filename;

            // Use a temporary file when downloading. Not using a temporary file can cause an error when an existing
            // file has already fully completed downloading and a new download is started with the same destination save
            // path. The download request will include header "Range: bytes=$filesize-" which is syntactically valid,
            // but unsatisfiable.
            $download_filename = $filename . '.pccdownload';
            $this->downloadFileName = $download_filename;

            // Attempt to resume download only when a temporary download file exists and is not empty.
            if (is_file($download_filename) && $filesize = filesize($download_filename)) {
                $first_byte_position = $filesize;
                $range = $first_byte_position . '-';
                $this->setRange($range);
                $this->fileHandle = fopen($download_filename, 'ab');
            } else {
                $this->fileHandle = fopen($download_filename, 'wb');
            }

            // Move the downloaded temporary file to the destination save path.
            $this->downloadCompleteCallback = function ($instance, $fh) use ($download_filename, $filename) {
                // Close the open file handle before renaming the file.
                if (is_resource($fh)) {
                    fclose($fh);
                }

                rename($download_filename, $filename);
            };
        }

        $this->setFile($this->fileHandle);
        $this->get($url);

        return ! $this->error;
    }

    /**
     * Fast download
     *
     * @access private
     * @param  $url
     * @param  $filename
     * @param  $connections
     *
     * @return boolean
     */
    public function _fastDownload($url, $filename, $connections = 4)
    {
        // First we need to retrive the 'Content-Length' header.
        // Use GET because not all hosts support HEAD requests.
        $this->setOpts([
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_NOBODY        => true,
            CURLOPT_HEADER        => true,
            CURLOPT_ENCODING      => '',
        ]);
        $this->setUrl($url);
        $this->exec();

        $content_length = isset($this->responseHeaders['Content-Length']) ?
            $this->responseHeaders['Content-Length'] : null;

        // If content length header is missing, use the normal download.
        if (!$content_length) {
            return $this->download($url, $filename);
        }

        // Try to divide chunk_size equally.
        $chunkSize = ceil($content_length / $connections);

        // First bytes.
        $offset = 0;
        $nextChunk = $chunkSize;

        // We need this later.
        $file_parts = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setConcurrency($connections);

        $multi_curl->error(function ($instance) {
            return false;
        });

        for ($i = 1; $i <= $connections; $i++) {
            // If last chunk then no need to supply it.
            // Range starts with 0, so subtract 1.
            $nextChunk = $i === $connections ? '' : $nextChunk - 1;

            // Create part file.
            $fpath = "$filename.part$i";
            if (is_file($fpath)) {
                unlink($fpath);
            }
            $fp = fopen($fpath, 'w');

            // Track all fileparts names; we need this later.
            $file_parts[] = $fpath;

            $curl = new Curl();
            $curl->setOpt(CURLOPT_ENCODING, '');
            $curl->setRange("$offset-$nextChunk");
            $curl->setFile($fp);
            $curl->disableTimeout(); // otherwise download may fail.
            $curl->setUrl($url);

            $curl->complete(function () use ($fp) {
                fclose($fp);
            });

            $multi_curl->addCurl($curl);

            if ($i !== $connections) {
                $offset = $nextChunk + 1; // Add 1 to match offset.
                $nextChunk = $nextChunk + $chunkSize;
            }
        }

        // let the magic begin.
        $multi_curl->start();

        // Concatenate chunks to single.
        if (is_file($filename)) {
            unlink($filename);
        }
        $mainfp = fopen($filename, 'w');
        foreach ($file_parts as $part) {
            $fp = fopen($part, 'r');
            stream_copy_to_stream($fp, $mainfp);
            fclose($fp);
            unlink($part);
        }
        fclose($mainfp);

        return true;
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
        $this->attempts += 1;

        if ($this->jsonDecoder === null) {
            $this->setDefaultJsonDecoder();
        }
        if ($this->xmlDecoder === null) {
            $this->setDefaultXmlDecoder();
        }

        if ($ch === null) {
            $this->responseCookies = [];
            $this->call($this->beforeSendCallback);
            $this->rawResponse = curl_exec($this->curl);
            $this->curlErrorCode = curl_errno($this->curl);
            $this->curlErrorMessage = curl_error($this->curl);
        } else {
            $this->rawResponse = curl_multi_getcontent($ch);
            $this->curlErrorMessage = curl_error($ch);
        }
        $this->curlError = $this->curlErrorCode !== 0;

        // Ensure Curl::rawResponse is a string as curl_exec() can return false.
        // Without this, calling strlen($curl->rawResponse) will error when the
        // strict types setting is enabled.
        if (!is_string($this->rawResponse)) {
            $this->rawResponse = '';
        }

        // Transfer the header callback data and release the temporary store to avoid memory leak.
        $this->rawResponseHeaders = $this->headerCallbackData->rawResponseHeaders;
        $this->responseCookies = $this->headerCallbackData->responseCookies;
        $this->headerCallbackData->rawResponseHeaders = '';
        $this->headerCallbackData->responseCookies = [];
        $this->headerCallbackData->stopRequestDecider = null;
        $this->headerCallbackData->stopRequest = false;

        // Include additional error code information in error message when possible.
        if ($this->curlError) {
            $curl_error_message = curl_strerror($this->curlErrorCode);

            if ($this->curlErrorCodeConstant !== '') {
                $curl_error_message .= ' (' . $this->curlErrorCodeConstant . ')';
            }

            if (!empty($this->curlErrorMessage)) {
                $curl_error_message .= ': ' . $this->curlErrorMessage;
            }

            $this->curlErrorMessage = $curl_error_message;
        }

        $this->httpStatusCode = $this->getInfo(CURLINFO_HTTP_CODE);
        $this->httpError = in_array((int) floor($this->httpStatusCode / 100), [4, 5], true);
        $this->error = $this->curlError || $this->httpError;
        $this->errorCode = $this->error ? ($this->curlError ? $this->curlErrorCode : $this->httpStatusCode) : 0;

        // NOTE: CURLINFO_HEADER_OUT set to true is required for requestHeaders
        // to not be empty (e.g. $curl->setOpt(CURLINFO_HEADER_OUT, true);).
        if ($this->getOpt(CURLINFO_HEADER_OUT) === true) {
            $this->requestHeaders = $this->parseRequestHeaders($this->getInfo(CURLINFO_HEADER_OUT));
        }
        $this->responseHeaders = $this->parseResponseHeaders($this->rawResponseHeaders);
        $this->response = $this->parseResponse($this->responseHeaders, $this->rawResponse);

        $this->httpErrorMessage = '';
        if ($this->error) {
            if (isset($this->responseHeaders['Status-Line'])) {
                $this->httpErrorMessage = $this->responseHeaders['Status-Line'];
            }
        }
        $this->errorMessage = $this->curlError ? $this->curlErrorMessage : $this->httpErrorMessage;

        // Reset select deferred properties so that they may be recalculated.
        unset($this->curlErrorCodeConstant);
        unset($this->effectiveUrl);
        unset($this->totalTime);

        // Reset content-length header possibly set from a PUT or SEARCH request.
        $this->unsetHeader('Content-Length');

        // Reset nobody setting possibly set from a HEAD request.
        $this->setOpt(CURLOPT_NOBODY, false);

        // Allow multicurl to attempt retry as needed.
        if ($this->isChildOfMultiCurl()) {
            return;
        }

        if ($this->attemptRetry()) {
            return $this->exec($ch);
        }

        $this->execDone();

        return $this->response;
    }

    public function execDone()
    {
        if ($this->error) {
            $this->call($this->errorCallback);
        } else {
            $this->call($this->successCallback);
        }

        $this->call($this->completeCallback);

        // Close open file handles and reset the curl instance.
        if ($this->fileHandle !== null) {
            $this->downloadComplete($this->fileHandle);
        }
    }

    /**
     * Get
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function get($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = (string)$this->url;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);
        return $this->exec();
    }

    /**
     * Get Info
     *
     * @access public
     * @param  $opt
     *
     * @return mixed
     */
    public function getInfo($opt = null)
    {
        $args = [];
        $args[] = $this->curl;

        if (func_num_args()) {
            $args[] = $opt;
        }

        return call_user_func_array('curl_getinfo', $args);
    }

    /**
     * Head
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function head($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = (string)$this->url;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        return $this->exec();
    }

    /**
     * Options
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function options($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = (string)$this->url;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    /**
     * Patch
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function patch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = (string)$this->url;
        }

        if (is_array($data) && empty($data)) {
            $this->removeHeader('Content-Length');
        }

        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Post
     *
     * @access public
     * @param  $url
     * @param  $data
     * @param  $follow_303_with_post
     *     If true, will cause 303 redirections to be followed using a POST request (default: false).
     *     Notes:
     *       - Redirections are only followed if the CURLOPT_FOLLOWLOCATION option is set to true.
     *       - According to the HTTP specs (see [1]), a 303 redirection should be followed using
     *         the GET method. 301 and 302 must not.
     *       - In order to force a 303 redirection to be performed using the same method, the
     *         underlying cURL object must be set in a special state (the CURLOPT_CUSTOMREQUEST
     *         option must be set to the method to use after the redirection). Due to a limitation
     *         of the cURL extension of PHP < 5.5.11 ([2], [3]), it is not possible to reset this
     *         option. Using these PHP engines, it is therefore impossible to restore this behavior
     *         on an existing php-curl-class Curl object.
     *
     * @return mixed Returns the value provided by exec.
     *
     * [1] https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.2
     * [2] https://github.com/php/php-src/pull/531
     * [3] http://php.net/ChangeLog-5.php#5.5.11
     */
    public function post($url, $data = '', $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool)$data;
            $data = $url;
            $url = (string)$this->url;
        }

        $this->setUrl($url);

        // Set the request method to "POST" when following a 303 redirect with
        // an additional POST request is desired. This is equivalent to setting
        // the -X, --request command line option where curl won't change the
        // request method according to the HTTP 30x response code.
        if ($follow_303_with_post) {
            $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        } elseif (isset($this->options[CURLOPT_CUSTOMREQUEST])) {
            // Unset the CURLOPT_CUSTOMREQUEST option so that curl does not use
            // a POST request after a post/redirect/get redirection. Without
            // this, curl will use the method string specified for all requests.
            $this->setOpt(CURLOPT_CUSTOMREQUEST, null);
        }

        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Put
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function put($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = (string)$this->url;
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            if (is_string($put_data)) {
                $this->setHeader('Content-Length', strlen($put_data));
            }
        }
        if (!empty($put_data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        }
        return $this->exec();
    }

    /**
     * Search
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function search($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = (string)$this->url;
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'SEARCH');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            if (is_string($put_data)) {
                $this->setHeader('Content-Length', strlen($put_data));
            }
        }
        if (!empty($put_data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        }
        return $this->exec();
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
        $this->setEncodedCookie($key, $value);
        $this->buildCookies();
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
            $this->setEncodedCookie($key, $value);
        }
        $this->buildCookies();
    }

    /**
     * Get Cookie
     *
     * @access public
     * @param  $key
     *
     * @return mixed
     */
    public function getCookie($key)
    {
        return $this->getResponseCookie($key);
    }

    /**
     * Get Response Cookie
     *
     * @access public
     * @param  $key
     *
     * @return mixed
     */
    public function getResponseCookie($key)
    {
        return isset($this->responseCookies[$key]) ? $this->responseCookies[$key] : null;
    }

    /**
     * Set Max Filesize
     *
     * @access public
     * @param  $bytes
     */
    public function setMaxFilesize($bytes)
    {
        $callback = function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($bytes) {
            // Abort the transfer when $downloaded bytes exceeds maximum $bytes by returning a non-zero value.
            return $downloaded > $bytes ? 1 : 0;
        };
        $this->progress($callback);
    }

    /**
     * Set Cookie String
     *
     * @access public
     * @param  $string
     *
     * @return bool
     */
    public function setCookieString($string)
    {
        return $this->setOpt(CURLOPT_COOKIE, $string);
    }

    /**
     * Set Cookie File
     *
     * @access public
     * @param  $cookie_file
     *
     * @return boolean
     */
    public function setCookieFile($cookie_file)
    {
        return $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * Set Cookie Jar
     *
     * @access public
     * @param  $cookie_jar
     *
     * @return boolean
     */
    public function setCookieJar($cookie_jar)
    {
        return $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * Set Default JSON Decoder
     *
     * @access public
     * @param  $assoc
     * @param  $depth
     * @param  $options
     */
    public function setDefaultJsonDecoder()
    {
        $this->jsonDecoder = '\Curl\Decoder::decodeJson';
        $this->jsonDecoderArgs = func_get_args();
    }

    /**
     * Set Default XML Decoder
     *
     * @access public
     * @param  $class_name
     * @param  $options
     * @param  $ns
     * @param  $is_prefix
     */
    public function setDefaultXmlDecoder()
    {
        $this->xmlDecoder = '\Curl\Decoder::decodeXml';
        $this->xmlDecoderArgs = func_get_args();
    }

    /**
     * Set Default Decoder
     *
     * @access public
     * @param  $mixed boolean|callable|string
     */
    public function setDefaultDecoder($mixed = 'json')
    {
        if ($mixed === false) {
            $this->defaultDecoder = false;
        } elseif ($mixed === 'json') {
            $this->defaultDecoder = '\Curl\Decoder::decodeJson';
        } elseif ($mixed === 'xml') {
            $this->defaultDecoder = '\Curl\Decoder::decodeXml';
        } elseif (is_callable($mixed)) {
            $this->defaultDecoder = $mixed;
        }
    }

    /**
     * Set Default Header Out
     *
     * @access public
     */
    public function setDefaultHeaderOut()
    {
        $this->setOpt(CURLINFO_HEADER_OUT, true);
    }

    /**
     * Set Default Timeout
     *
     * @access public
     */
    public function setDefaultTimeout()
    {
        $this->setTimeout(self::DEFAULT_TIMEOUT);
    }

    /**
     * Set Default User Agent
     *
     * @access public
     */
    public function setDefaultUserAgent()
    {
        $user_agent = 'PHP-Curl-Class/' . self::VERSION . ' (+https://github.com/php-curl-class/php-curl-class)';
        $user_agent .= ' PHP/' . PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/' . $curl_version['version'];
        $this->setUserAgent($user_agent);
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
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
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

        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Set JSON Decoder
     *
     * @access public
     * @param  $mixed boolean|callable
     */
    public function setJsonDecoder($mixed)
    {
        if ($mixed === false || is_callable($mixed)) {
            $this->jsonDecoder = $mixed;
            $this->jsonDecoderArgs = [];
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
        if ($mixed === false || is_callable($mixed)) {
            $this->xmlDecoder = $mixed;
            $this->xmlDecoderArgs = [];
        }
    }

    /**
     * Set Opt
     *
     * @access public
     * @param  $option
     * @param  $value
     *
     * @return boolean
     */
    public function setOpt($option, $value)
    {
        $required_options = [
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        ];

        if (in_array($option, array_keys($required_options), true) && $value !== true) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $success = curl_setopt($this->curl, $option, $value);
        if ($success) {
            $this->options[$option] = $value;
        }
        return $success;
    }

    /**
     * Set Opts
     *
     * @access public
     * @param  $options
     *
     * @return boolean
     *   Returns true if all options were successfully set. If an option could not be successfully set, false is
     *   immediately returned, ignoring any future options in the options array. Similar to curl_setopt_array().
     */
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            if (!$this->setOpt($option, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set Protocols
     *
     * Limit what protocols libcurl will accept for a request.
     *
     * @access public
     * @param  $protocols
     * @see    Curl::setRedirectProtocols()
     */
    public function setProtocols($protocols)
    {
        $this->setOpt(CURLOPT_PROTOCOLS, $protocols);
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
        if (is_callable($mixed)) {
            $this->retryDecider = $mixed;
        } elseif (is_int($mixed)) {
            $maximum_number_of_retries = $mixed;
            $this->remainingRetries = $maximum_number_of_retries;
        }
    }

    /**
     * Set Redirect Protocols
     *
     * Limit what protocols libcurl will accept when following a redirect.
     *
     * @access public
     * @param  $redirect_protocols
     * @see    Curl::setProtocols()
     */
    public function setRedirectProtocols($redirect_protocols)
    {
        $this->setOpt(CURLOPT_REDIR_PROTOCOLS, $redirect_protocols);
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

        if ($this->url === null) {
            $this->url = (string)new Url($built_url);
        } else {
            $this->url = (string)new Url($this->url, $built_url);
        }

        $this->setOpt(CURLOPT_URL, $this->url);
    }

    /**
     * Attempt Retry
     *
     * @access public
     */
    public function attemptRetry()
    {
        $attempt_retry = false;
        if ($this->error) {
            if ($this->retryDecider === null) {
                $attempt_retry = $this->remainingRetries >= 1;
            } else {
                $attempt_retry = call_user_func($this->retryDecider, $this);
            }
            if ($attempt_retry) {
                $this->retries += 1;
                if ($this->remainingRetries) {
                    $this->remainingRetries -= 1;
                }
            }
        }
        return $attempt_retry;
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
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Diagnose
     *
     * @access public
     * @param  bool $return
     */
    public function diagnose($return = false)
    {
        if ($return) {
            ob_start();
        }

        echo "\n";
        echo '--- Begin PHP Curl Class diagnostic output ---' . "\n";
        echo 'PHP Curl Class version: ' . self::VERSION . "\n";
        echo 'PHP version: ' . PHP_VERSION . "\n";

        $curl_version = curl_version();
        echo 'Curl version: ' . $curl_version['version'] . "\n";

        if ($this->attempts === 0) {
            echo 'No HTTP requests have been made.' . "\n";
        } else {
            $request_types = array(
                'DELETE' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'DELETE',
                'GET' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'GET' || $this->getOpt(CURLOPT_HTTPGET),
                'HEAD' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'HEAD',
                'OPTIONS' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'OPTIONS',
                'PATCH' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'PATCH',
                'POST' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'POST' || $this->getOpt(CURLOPT_POST),
                'PUT' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'PUT',
                'SEARCH' => $this->getOpt(CURLOPT_CUSTOMREQUEST) === 'SEARCH',
            );
            $request_method = '';
            foreach ($request_types as $http_method_name => $http_method_used) {
                if ($http_method_used) {
                    $request_method = $http_method_name;
                    break;
                }
            }
            $request_url = $this->getOpt(CURLOPT_URL);
            $request_options_count = count($this->options);
            $request_headers_count = count($this->requestHeaders);
            $request_body_empty = empty($this->getOpt(CURLOPT_POSTFIELDS));
            $response_header_length = isset($this->responseHeaders['Content-Length']) ?
                $this->responseHeaders['Content-Length'] : '(not specified in response header)';
            $response_calculated_length = is_string($this->rawResponse) ?
                strlen($this->rawResponse) : '(' . var_export($this->rawResponse, true) . ')';
            $response_headers_count = count($this->responseHeaders);

            echo
                'Request contained ' . $request_options_count . ' ' . (
                    $request_options_count === 1 ? 'option:' : 'options:'
                ) . "\n";
            if ($request_options_count) {
                $i = 1;
                foreach ($this->options as $option => $value) {
                    echo '    ' . $i . ' ';
                    if (isset($this->curlOptionCodeConstants[$option])) {
                        echo $this->curlOptionCodeConstants[$option] . ':';
                    } else {
                        echo $option . ':';
                    }

                    if (is_string($value)) {
                        echo ' ' . $value . "\n";
                    } elseif (is_int($value)) {
                        echo ' ' . $value . "\n";
                    } elseif (is_bool($value)) {
                        echo ' ' . ($value ? 'true' : 'false') . "\n";
                    } elseif (is_callable($value)) {
                        echo ' (callable)' . "\n";
                    } else {
                        echo ' ' . gettype($value) . ':' . "\n";
                        var_dump($value);
                    }
                    $i += 1;
                }
            }

            echo
                'Sent an HTTP '   . $request_method . ' request to "' . $request_url . '".' . "\n" .
                'Request contained ' . $request_headers_count . ' ' . (
                    $request_headers_count === 1 ? 'header:' : 'headers:'
                ) . "\n";
            if ($request_headers_count) {
                $i = 1;
                foreach ($this->requestHeaders as $key => $value) {
                    echo '    ' . $i . ' ' . $key . ': ' . $value . "\n";
                    $i += 1;
                }
            }

            echo 'Request contained ' . ($request_body_empty ? 'no body' : 'a body') . '.' . "\n";

            if ($request_headers_count === 0 && (
                $this->getOpt(CURLOPT_VERBOSE) ||
                !$this->getOpt(CURLINFO_HEADER_OUT)
            )) {
                echo
                    'Warning: Request headers (Curl::requestHeaders) are expected to be empty ' .
                    '(CURLOPT_VERBOSE was enabled or CURLINFO_HEADER_OUT was disabled).' . "\n";
            }

            if (isset($this->responseHeaders['allow'])) {
                $allowed_request_types = array_map(function ($v) {
                    return trim($v);
                }, explode(',', strtoupper($this->responseHeaders['allow'])));
                foreach ($request_types as $http_method_name => $http_method_used) {
                    if ($http_method_used && !in_array($http_method_name, $allowed_request_types, true)) {
                        echo
                            'Warning: A ' . $http_method_name . ' request was made, but only the following request ' .
                            'types are allowed: ' . implode(', ', $allowed_request_types) . "\n";
                    }
                }
            }

            echo
                'Response contains ' . $response_headers_count . ' ' . (
                    $response_headers_count === 1 ? 'header:' : 'headers:'
                ) . "\n";
            if ($this->responseHeaders !== null) {
                $i = 1;
                foreach ($this->responseHeaders as $key => $value) {
                    echo '    ' . $i . ' ' . $key . ': ' . $value . "\n";
                    $i += 1;
                }
            }

            if (!isset($this->responseHeaders['Content-Type'])) {
                echo 'Response did not set a content type.' . "\n";
            } elseif (preg_match($this->jsonPattern, $this->responseHeaders['Content-Type'])) {
                echo 'Response appears to be JSON.' . "\n";
            } elseif (preg_match($this->xmlPattern, $this->responseHeaders['Content-Type'])) {
                echo 'Response appears to be XML.' . "\n";
            }

            if ($this->curlError) {
                echo
                    'A curl error (' . $this->curlErrorCode . ') occurred ' .
                    'with message "' . $this->curlErrorMessage . '".' . "\n";
            }
            if (!empty($this->httpStatusCode)) {
                echo 'Received an HTTP status code of ' . $this->httpStatusCode . '.' . "\n";
            }
            if ($this->httpError) {
                echo
                    'Received an HTTP ' . $this->httpStatusCode . ' error response ' .
                    'with message "' . $this->httpErrorMessage . '".' . "\n";
            }

            if ($this->rawResponse === null) {
                echo 'Received no response body (response=null).' . "\n";
            } elseif ($this->rawResponse === '') {
                echo 'Received an empty response body (response="").' . "\n";
            } else {
                echo 'Received a non-empty response body.' . "\n";
                if (isset($this->responseHeaders['Content-Length'])) {
                    echo 'Response content length (from content-length header): ' . $response_header_length . "\n";
                } else {
                    echo 'Response content length (calculated): ' . $response_calculated_length . "\n";
                }

                if (preg_match($this->jsonPattern, $this->responseHeaders['Content-Type'])) {
                    $parsed_response = json_decode($this->rawResponse, true);
                    if ($parsed_response !== null) {
                        $messages = [];
                        array_walk_recursive($parsed_response, function ($value, $key) use (&$messages) {
                            if (in_array($key, ['code', 'error', 'message'], true)) {
                                $message = $key . ': ' . $value;
                                $messages[] = $message;
                            }
                        });
                        $messages = array_unique($messages);

                        $messages_count = count($messages);
                        if ($messages_count) {
                            echo
                                'Found ' . $messages_count . ' ' . ($messages_count === 1 ? 'message' : 'messages') .
                                ' in response:' . "\n";

                            $i = 1;
                            foreach ($messages as $message) {
                                echo '    ' . $i . ' ' . $message . "\n";
                                $i += 1;
                            }
                        }
                    }
                }
            }
        }

        echo '--- End PHP Curl Class diagnostic output ---' . "\n";
        echo "\n";

        if ($return) {
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }

    /**
     * Reset
     *
     * @access public
     */
    public function reset()
    {
        if (is_resource($this->curl) || $this->curl instanceof \CurlHandle) {
            curl_reset($this->curl);
        } else {
            $this->curl = curl_init();
        }

        $this->setDefaultUserAgent();
        $this->setDefaultTimeout();
        $this->setDefaultHeaderOut();

        $this->initialize();
    }

    public function getCurl()
    {
        return $this->curl;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isError()
    {
        return $this->error;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function isCurlError()
    {
        return $this->curlError;
    }

    public function getCurlErrorCode()
    {
        return $this->curlErrorCode;
    }

    public function getCurlErrorMessage()
    {
        return $this->curlErrorMessage;
    }

    public function isHttpError()
    {
        return $this->httpError;
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function getHttpErrorMessage()
    {
        return $this->httpErrorMessage;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function getRawResponseHeaders()
    {
        return $this->rawResponseHeaders;
    }

    public function getResponseCookies()
    {
        return $this->responseCookies;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public function getBeforeSendCallback()
    {
        return $this->beforeSendCallback;
    }

    public function getDownloadCompleteCallback()
    {
        return $this->downloadCompleteCallback;
    }

    public function getDownloadFileName()
    {
        return $this->downloadFileName;
    }

    public function getSuccessCallback()
    {
        return $this->successCallback;
    }

    public function getErrorCallback()
    {
        return $this->errorCallback;
    }

    public function getCompleteCallback()
    {
        return $this->completeCallback;
    }

    public function getFileHandle()
    {
        return $this->fileHandle;
    }

    public function getAttempts()
    {
        return $this->attempts;
    }

    public function getRetries()
    {
        return $this->retries;
    }

    public function isChildOfMultiCurl()
    {
        return $this->childOfMultiCurl;
    }

    public function getRemainingRetries()
    {
        return $this->remainingRetries;
    }

    public function getRetryDecider()
    {
        return $this->retryDecider;
    }

    public function getJsonDecoder()
    {
        return $this->jsonDecoder;
    }

    public function getXmlDecoder()
    {
        return $this->xmlDecoder;
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

    public function __get($name)
    {
        $return = null;
        if (in_array($name, self::$deferredProperties, true) &&
            is_callable([$this, $getter = '_get' . ucfirst($name)])) {
            $return = $this->$name = $this->$getter();
        }
        return $return;
    }

    /**
     * Get Curl Error Code Constants
     *
     * @access private
     */
    private function _getCurlErrorCodeConstants()
    {
        $constants = get_defined_constants(true);
        $filtered_array = array_filter(
            $constants['curl'],
            function ($key) {
                return strpos($key, 'CURLE_') !== false;
            },
            ARRAY_FILTER_USE_KEY
        );
        $curl_const_by_code = array_flip($filtered_array);
        return $curl_const_by_code;
    }

    /**
     * Get Curl Error Code Constant
     *
     * @access private
     */
    private function _getCurlErrorCodeConstant()
    {
        $curl_const_by_code = $this->curlErrorCodeConstants;
        if (isset($curl_const_by_code[$this->curlErrorCode])) {
            return $curl_const_by_code[$this->curlErrorCode];
        }
        return '';
    }

    /**
     * Get Curl Option Code Constants
     *
     * @access private
     */
    private function _getCurlOptionCodeConstants()
    {
        $constants = get_defined_constants(true);
        $filtered_array = array_filter(
            $constants['curl'],
            function ($key) {
                return strpos($key, 'CURLOPT_') !== false;
            },
            ARRAY_FILTER_USE_KEY
        );
        $curl_const_by_code = array_flip($filtered_array);

        if (!isset($curl_const_by_code[CURLINFO_HEADER_OUT])) {
            $curl_const_by_code[CURLINFO_HEADER_OUT] = 'CURLINFO_HEADER_OUT';
        }

        return $curl_const_by_code;
    }

    /**
     * Get Effective Url
     *
     * @access private
     */
    private function _getEffectiveUrl()
    {
        return $this->getInfo(CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Get RFC 2616
     *
     * @access private
     */
    private function _getRfc2616()
    {
        return array_fill_keys(self::$RFC2616, true);
    }

    /**
     * Get RFC 6265
     *
     * @access private
     */
    private function _getRfc6265()
    {
        return array_fill_keys(self::$RFC6265, true);
    }

    /**
     * Get Total Time
     *
     * @access private
     */
    private function _getTotalTime()
    {
        return $this->getInfo(CURLINFO_TOTAL_TIME);
    }

    /**
     * Build Cookies
     *
     * @access private
     */
    private function buildCookies()
    {
        // Avoid changing CURLOPT_COOKIE if there are no cookies set.
        if (count($this->cookies)) {
            // Avoid using http_build_query() as unnecessary encoding is performed.
            // http_build_query($this->cookies, '', '; ');
            $this->setOpt(CURLOPT_COOKIE, implode('; ', array_map(function ($k, $v) {
                return $k . '=' . $v;
            }, array_keys($this->cookies), array_values($this->cookies))));
        }
    }

    /**
     * Download Complete
     *
     * @access private
     * @param  $fh
     */
    private function downloadComplete($fh)
    {
        if ($this->error && is_file((string) $this->downloadFileName)) {
            @unlink($this->downloadFileName);
        } elseif (!$this->error && $this->downloadCompleteCallback) {
            rewind($fh);
            $this->call($this->downloadCompleteCallback, $fh);
            $this->downloadCompleteCallback = null;
        }

        if (is_resource($fh)) {
            fclose($fh);
        }

        // Fix "PHP Notice: Use of undefined constant STDOUT" when reading the
        // PHP script from stdin. Using null causes "Warning: curl_setopt():
        // supplied argument is not a valid File-Handle resource".
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }

        // Reset CURLOPT_FILE with STDOUT to avoid: "curl_exec(): CURLOPT_FILE
        // resource has gone away, resetting to default".
        $this->setFile(STDOUT);

        // Reset CURLOPT_RETURNTRANSFER to tell cURL to return subsequent
        // responses as the return value of curl_exec(). Without this,
        // curl_exec() will revert to returning boolean values.
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Parse Headers
     *
     * @access private
     * @param  $raw_headers
     *
     * @return array
     */
    private function parseHeaders($raw_headers)
    {
        $raw_headers = preg_split('/\r\n/', (string) $raw_headers, -1, PREG_SPLIT_NO_EMPTY);
        $http_headers = new CaseInsensitiveArray();

        $raw_headers_count = count($raw_headers);
        for ($i = 1; $i < $raw_headers_count; $i++) {
            if (strpos($raw_headers[$i], ':') !== false) {
                list($key, $value) = explode(':', $raw_headers[$i], 2);
                $key = trim($key);
                $value = trim($value);
                // Use isset() as array_key_exists() and ArrayAccess are not compatible.
                if (isset($http_headers[$key])) {
                    $http_headers[$key] .= ',' . $value;
                } else {
                    $http_headers[$key] = $value;
                }
            }
        }

        return [isset($raw_headers['0']) ? $raw_headers['0'] : '', $http_headers];
    }

    /**
     * Parse Request Headers
     *
     * @access private
     * @param  $raw_headers
     *
     * @return \Curl\CaseInsensitiveArray
     */
    private function parseRequestHeaders($raw_headers)
    {
        $request_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($raw_headers);
        $request_headers['Request-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $request_headers[$key] = $value;
        }
        return $request_headers;
    }

    /**
     * Parse Response
     *
     * @access private
     * @param  $response_headers
     * @param  $raw_response
     *
     * @return mixed
     *   If the response content-type is json:
     *     Returns the json decoder's return value: A stdClass object when the default json decoder is used.
     *   If the response content-type is xml:
     *     Returns the xml decoder's return value: A SimpleXMLElement object when the default xml decoder is used.
     *   If the response content-type is something else:
     *     Returns the original raw response unless a default decoder has been set.
     *   If the response content-type cannot be determined:
     *     Returns the original raw response.
     *   If the response content-encoding is gzip:
     *     Returns the response gzip-decoded.
     */
    private function parseResponse($response_headers, $raw_response)
    {
        $response = $raw_response;
        if (isset($response_headers['Content-Type'])) {
            if (preg_match($this->jsonPattern, $response_headers['Content-Type'])) {
                if ($this->jsonDecoder) {
                    $args = $this->jsonDecoderArgs;
                    array_unshift($args, $response);
                    $response = call_user_func_array($this->jsonDecoder, $args);
                }
            } elseif (preg_match($this->xmlPattern, $response_headers['Content-Type'])) {
                if ($this->xmlDecoder) {
                    $args = $this->xmlDecoderArgs;
                    array_unshift($args, $response);
                    $response = call_user_func_array($this->xmlDecoder, $args);
                }
            } else {
                if ($this->defaultDecoder) {
                    $response = call_user_func($this->defaultDecoder, $response);
                }
            }
        }

        if (isset($response_headers['Content-Encoding']) && $response_headers['Content-Encoding'] === 'gzip' &&
            is_string($response)) {
            // Use @ to suppress message "Warning gzdecode(): data error".
            $decoded_response = @gzdecode($response);
            if ($decoded_response !== false) {
                $response = $decoded_response;
            }
        }

        return $response;
    }

    /**
     * Parse Response Headers
     *
     * @access private
     * @param  $raw_response_headers
     *
     * @return \Curl\CaseInsensitiveArray
     */
    private function parseResponseHeaders($raw_response_headers)
    {
        $response_header_array = explode("\r\n\r\n", $raw_response_headers);
        $response_header  = '';
        for ($i = count($response_header_array) - 1; $i >= 0; $i--) {
            if (isset($response_header_array[$i]) && stripos($response_header_array[$i], 'HTTP/') === 0) {
                $response_header = $response_header_array[$i];
                break;
            }
        }

        $response_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($response_header);
        $response_headers['Status-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $response_headers[$key] = $value;
        }
        return $response_headers;
    }

    /**
     * Set Encoded Cookie
     *
     * @access private
     * @param  $key
     * @param  $value
     */
    private function setEncodedCookie($key, $value)
    {
        $name_chars = [];
        foreach (str_split($key) as $name_char) {
            if (isset($this->rfc2616[$name_char])) {
                $name_chars[] = $name_char;
            } else {
                $name_chars[] = rawurlencode($name_char);
            }
        }

        $value_chars = [];
        foreach (str_split($value) as $value_char) {
            if (isset($this->rfc6265[$value_char])) {
                $value_chars[] = $value_char;
            } else {
                $value_chars[] = rawurlencode($value_char);
            }
        }

        $this->cookies[implode('', $name_chars)] = implode('', $value_chars);
    }

    /**
     * Initialize
     *
     * @access private
     * @param  $base_url
     */
    private function initialize($base_url = null, $options = [])
    {
        $this->setProtocols(CURLPROTO_HTTPS | CURLPROTO_HTTP);
        $this->setRedirectProtocols(CURLPROTO_HTTPS | CURLPROTO_HTTP);

        if (isset($options)) {
            $this->setOpts($options);
        }

        $this->id = uniqid('', true);

        // Only set default user agent if not already set.
        if (!array_key_exists(CURLOPT_USERAGENT, $this->options)) {
            $this->setDefaultUserAgent();
        }

        // Only set default timeout if not already set.
        if (!array_key_exists(CURLOPT_TIMEOUT, $this->options)) {
            $this->setDefaultTimeout();
        }

        if (!array_key_exists(CURLINFO_HEADER_OUT, $this->options)) {
            $this->setDefaultHeaderOut();
        }

        // Create a placeholder to temporarily store the header callback data.
        $header_callback_data = new \stdClass();
        $header_callback_data->rawResponseHeaders = '';
        $header_callback_data->responseCookies = [];
        $header_callback_data->stopRequestDecider = null;
        $header_callback_data->stopRequest = false;
        $this->headerCallbackData = $header_callback_data;
        $this->setStop();
        $this->setOpt(CURLOPT_HEADERFUNCTION, createHeaderCallback($header_callback_data));

        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->headers = new CaseInsensitiveArray();

        if ($base_url !== null) {
            $this->setUrl($base_url);
        }
    }

    /**
     * Set Stop
     *
     * Specify a callable decider to stop the request early without waiting for
     * the full response to be received.
     *
     * The callable is passed two parameters. The first is the cURL resource,
     * the second is a string with header data. Both parameters match the
     * parameters in the CURLOPT_HEADERFUNCTION callback.
     *
     * The callable must return a truthy value for the request to be stopped
     * early.
     *
     * The callable may be set to null to avoid calling the stop request decider
     * callback and instead just check the value of stopRequest for attempting
     * to stop the request as used by Curl::stop().
     *
     * @access public
     * @param  $callback callable|null
     */
    public function setStop($callback = null)
    {
        $this->headerCallbackData->stopRequestDecider = $callback;
        $this->headerCallbackData->stopRequest = false;

        $header_callback_data = $this->headerCallbackData;
        $this->progress(createStopRequestFunction($header_callback_data));
    }

    /**
     * Stop
     *
     * Attempt to stop request.
     *
     * Used by MultiCurl::stop() when making multiple parallel requests.
     *
     * @access public
     */
    public function stop()
    {
        $this->headerCallbackData->stopRequest = true;
    }
}

/**
 * Create Header Callback
 *
 * Gather headers and parse cookies as response headers are received. Keep this function separate from the class so that
 * unset($curl) automatically calls __destruct() as expected. Otherwise, manually calling $curl->close() will be
 * necessary to prevent a memory leak.
 *
 * @param  $header_callback_data
 *
 * @return callable
 */
function createHeaderCallback($header_callback_data)
{
    return function ($ch, $header) use ($header_callback_data) {
        if (preg_match('/^Set-Cookie:\s*([^=]+)=([^;]+)/mi', $header, $cookie) === 1) {
            $header_callback_data->responseCookies[$cookie[1]] = trim($cookie[2], " \n\r\t\0\x0B");
        }

        if ($header_callback_data->stopRequestDecider !== null) {
            $stop_request_decider = $header_callback_data->stopRequestDecider;
            if ($stop_request_decider($ch, $header)) {
                $header_callback_data->stopRequest = true;
            }
        }

        $header_callback_data->rawResponseHeaders .= $header;
        return strlen($header);
    };
}

/**
 * Create Stop Request Function
 *
 * Create a function for Curl::progress() that stops a request early when the
 * stopRequest flag is on. Keep this function separate from the class to prevent
 * a memory leak.
 *
 * @param  $header_callback_data
 *
 * @return callable
 */
function createStopRequestFunction($header_callback_data)
{
    return function (
        $resource,
        $download_size,
        $downloaded,
        $upload_size,
        $uploaded
    ) use (
        $header_callback_data
    ) {
        // Abort the transfer when the stop request flag has been set by returning a non-zero value.
        return $header_callback_data->stopRequest ? 1 : 0;
    };
}
