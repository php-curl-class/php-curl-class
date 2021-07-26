<?php declare(strict_types=1);

namespace Curl;

class Encoder
{
    /**
     * Encode JSON
     *
     * Wrap json_encode() to throw error when the value being encoded fails.
     *
     * @access public
     * @param  $value
     * @param  $options
     * @param  $depth
     *
     * @return string
     * @throws \ErrorException
     */
    public static function encodeJson()
    {
        $args = func_get_args();
        $value = call_user_func_array('json_encode', $args);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'json_encode error: ' . json_last_error_msg();
            throw new \ErrorException($error_message);
        }
        return $value;
    }
}
