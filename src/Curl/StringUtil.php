<?php declare(strict_types=1);

namespace Curl;

class StringUtil
{
    public static function characterReversePosition($haystack, $needle, $part = false)
    {
        if (function_exists('\mb_strrchr')) {
            return \mb_strrchr($haystack, $needle, $part);
        } else {
            return \strrchr($haystack, $needle);
        }
    }

    public static function length($string)
    {
        if (function_exists('\mb_strlen')) {
            return \mb_strlen($string);
        } else {
            return \strlen($string);
        }
    }

    public static function position($haystack, $needle, $offset = 0)
    {
        if (function_exists('\mb_strpos')) {
            return \mb_strpos($haystack, $needle, $offset);
        } else {
            return \strpos($haystack, $needle, $offset);
        }
    }

    public static function reversePosition($haystack, $needle, $offset = 0)
    {
        if (function_exists('\mb_strrpos')) {
            return \mb_strrpos($haystack, $needle, $offset);
        } else {
            return \strrpos($haystack, $needle, $offset);
        }
    }

    /**
     * Return true when $haystack starts with $needle.
     *
     * @access public
     * @param  $haystack
     * @param  $needle
     *
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return self::substring($haystack, 0, self::length($needle)) === $needle;
    }

    public static function substring($string, $start, $length)
    {
        if (function_exists('\mb_substr')) {
            return \mb_substr($string, $start, $length);
        } else {
            return \substr($string, $start, $length);
        }
    }
}
