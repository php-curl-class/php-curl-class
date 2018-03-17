<?php

namespace Curl\Errors;

use Curl\Curl;

class CurlLockedException extends \Exception
{
    public function __construct(Curl $curl, $code = 0, \Throwable $previous = null)
    {
        $message = "Curl #{$curl->id} is locked.";
        parent::__construct($message, $code, $previous);
    }
}
