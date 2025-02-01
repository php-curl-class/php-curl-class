<?php

declare(strict_types=1);

namespace Curl;

class Decoder
{
    /**
     * Decode JSON
     *
     * @param $json
     * @param $assoc
     * @param $depth
     * @param $options
     */
    public static function decodeJson()
    {
        $args = func_get_args();
        $response = call_user_func_array('json_decode', $args);
        if ($response === null && isset($args['0'])) {
            $response = $args['0'];
        }
        return $response;
    }

    /**
     * Decode XML
     *
     * @param $data
     * @param $class_name
     * @param $options
     * @param $ns
     * @param $is_prefix
     */
    public static function decodeXml()
    {
        $args = func_get_args();
        $response = @call_user_func_array('simplexml_load_string', $args);
        if ($response === false && array_key_exists('0', $args)) {
            $response = $args['0'];
        }
        return $response;
    }
}
