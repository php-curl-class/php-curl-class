<?php

namespace Curl;

use Curl\CaseInsensitiveArray;

class ArrayUtil
{
    /**
     * Is Array Assoc
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function isArrayAssoc($array)
    {
        return (
            $array instanceof CaseInsensitiveArray ||
            (bool)count(array_filter(array_keys($array), 'is_string'))
        );
    }

    /**
     * Is Array Assoc
     *
     * @deprecated Use ArrayUtil::isArrayAssoc().
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_assoc($array)
    {
        return $this->isArrayAssoc($array);
    }

    /**
     * Is Array Multidim
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function isArrayMultidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return (bool)count(array_filter($array, 'is_array'));
    }

    /**
     * Is Array Multidim
     *
     * @deprecated Use ArrayUtil::isArrayMultidim().
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_multidim($array)
    {
        return $this->isArrayMultidim($array);
    }

    /**
     * Array Flatten Multidim
     *
     * @access public
     * @param  $array
     * @param  $prefix
     *
     * @return array
     */
    public static function arrayFlattenMultidim($array, $prefix = false)
    {
        $return = array();
        if (is_array($array) || is_object($array)) {
            if (empty($array)) {
                $return[$prefix] = '';
            } else {
                foreach ($array as $key => $value) {
                    if (is_scalar($value)) {
                        if ($prefix) {
                            $return[$prefix . '[' . $key . ']'] = $value;
                        } else {
                            $return[$key] = $value;
                        }
                    } else {
                        if ($value instanceof \CURLFile) {
                            $return[$key] = $value;
                        } else {
                            $return = array_merge(
                                $return,
                                self::arrayFlattenMultidim(
                                    $value,
                                    $prefix ? $prefix . '[' . $key . ']' : $key
                                )
                            );
                        }
                    }
                }
            }
        } elseif ($array === null) {
            $return[$prefix] = $array;
        }
        return $return;
    }

    /**
     * Array Flatten Multidim
     *
     * @deprecated Use ArrayUtil::arrayFlattenMultidim().
     * @access public
     * @param  $array
     * @param  $prefix
     *
     * @return array
     */
    public static function array_flatten_multidim($array, $prefix = false)
    {
        return $this->arrayFlattenMultidim($array, $prefix);
    }

    /**
     * Array Random
     *
     * @access public
     * @param  $array
     *
     * @return mixed
     */
    public static function arrayRandom($array)
    {
        return $array[mt_rand(0, count($array) - 1)];
    }

    /**
     * Array Random
     *
     * @deprecated Use ArrayUtil::arrayRandom().
     * @access public
     * @param  $array
     *
     * @return mixed
     */
    public static function array_random($array)
    {
        return $this->arrayRandom($array);
    }
}
