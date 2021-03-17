<?php

namespace Curl;

class Decoder
{
    /**
     * Decode JSON
     *
     * @access public
     * @param  $json
     * @param  $assoc
     * @param  $depth
     * @param  $options
     */
    public static function decodeJson()
    {
        $args = func_get_args();
        $response = call_user_func_array('json_decode', $args);
        if ($response === null) {
            $response = $args['0'];
        }
        return $response;
    }

    /**
     * Decode XML
     *
     * @access public
     * @param  $data
     * @param  $class_name
     * @param  $options
     * @param  $ns
     * @param  $is_prefix
     */
    public static function decodeXml()
    {
        $args = func_get_args();
        $response = @call_user_func_array('simplexml_load_string', $args);
        if ($response === false) {
            $response = $args['0'];
        }
        return $response;
    }
}
