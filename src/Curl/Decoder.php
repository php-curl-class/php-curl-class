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

        // Call json_decode() without the $options parameter in PHP
        // versions less than 5.4.0 as the $options parameter was added in
        // PHP version 5.4.0.
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $args = array_slice($args, 0, 3);
        }

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
     * @param  $response
     */
    public static function decodeXml($response)
    {
        $args = func_get_args();
        $response = array_shift($args);

        $xml_obj = @simplexml_load_string($response);
        if ($xml_obj !== false) {
            $response = static::castXmlResponse(array_shift($args), $xml_obj);
        }

        return $response;
    }

    /**
     * Cast a response data to a native PHP type.
     *
     * @access public
     * @param  string $type
     * @param  mixed $value
     * @return mixed
     */
    public static function castXmlResponse($type, $value)
    {
        switch ($type) {
            case 'array':
                return static::object2array($value);
            default:
                return $value;
        }
    }

    /**
     * Translate object to array.
     *
     * @access public
     * @param  mixed $var
     * @return mixed
     */
    public static function object2array($var)
    {
        if (is_object($var)) {
            $var = get_object_vars($var);
        }

        if (is_array($var)) {
            return array_map(__METHOD__, $var);
        } else {
            return $var;
        }
    }
}
