<?php

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

        // Call json_encode() without the $depth parameter in PHP
        // versions less than 5.5.0 as the $depth parameter was added in
        // PHP version 5.5.0.
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $args = array_slice($args, 0, 2);
        }

        $value = call_user_func_array('json_encode', $args);
        if (!(json_last_error() === JSON_ERROR_NONE)) {
            if (function_exists('json_last_error_msg')) {
                $error_message = 'json_encode error: ' . json_last_error_msg();
            } else {
                $error_message = 'json_encode error';
            }
            throw new \ErrorException($error_message);
        }
        return $value;
    }
}
