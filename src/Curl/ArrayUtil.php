<?php

declare(strict_types=1);

namespace Curl;

class ArrayUtil
{
    /**
     * Is Array Assoc
     *
     * @param       $array
     * @return bool
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
     * @param       $array
     * @return bool
     */
    public static function is_array_assoc($array)
    {
        return static::isArrayAssoc($array);
    }

    /**
     * Is Array Multidim
     *
     * @param       $array
     * @return bool
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
     * @param       $array
     * @return bool
     */
    public static function is_array_multidim($array)
    {
        return static::isArrayMultidim($array);
    }

    /**
     * Array Flatten Multidim
     *
     * @param        $array
     * @param        $prefix
     * @return array
     */
    public static function arrayFlattenMultidim($array, $prefix = false)
    {
        $return = [];
        if (is_array($array) || is_object($array)) {
            if (empty($array)) {
                $return[$prefix] = '';
            } else {
                $arrays_to_merge = [];

                foreach ($array as $key => $value) {
                    if (is_scalar($value)) {
                        if ($prefix) {
                            $arrays_to_merge[] = [
                                $prefix . '[' . $key . ']' => $value,
                            ];
                        } else {
                            $arrays_to_merge[] = [
                                $key => $value,
                            ];
                        }
                    } elseif ($value instanceof \CURLFile) {
                        $arrays_to_merge[] = [
                            $key => $value,
                        ];
                    } elseif ($value instanceof \CURLStringFile) {
                        $arrays_to_merge[] = [
                            $key => $value,
                        ];
                    } else {
                        $arrays_to_merge[] = self::arrayFlattenMultidim(
                            $value,
                            $prefix ? $prefix . '[' . $key . ']' : $key
                        );
                    }
                }

                $return = array_merge($return, ...$arrays_to_merge);
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
     * @param        $array
     * @param        $prefix
     * @return array
     */
    public static function array_flatten_multidim($array, $prefix = false)
    {
        return static::arrayFlattenMultidim($array, $prefix);
    }

    /**
     * Array Random
     *
     * @param        $array
     * @return mixed
     */
    public static function arrayRandom($array)
    {
        return $array[static::arrayRandomIndex($array)];
    }

    /**
     * Array Random Index
     *
     * @param      $array
     * @return int
     */
    public static function arrayRandomIndex($array)
    {
        return mt_rand(0, count($array) - 1);
    }

    /**
     * Array Random
     *
     * @deprecated Use ArrayUtil::arrayRandom().
     * @param        $array
     * @return mixed
     */
    public static function array_random($array)
    {
        return static::arrayRandom($array);
    }
}
