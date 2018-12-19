<?php

namespace Curl;

class StringUtil
{
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
        return (function_exists("\mb_substr") ? (\mb_substr($haystack, 0, \mb_strlen($needle)) === $needle) : (\substr($haystack, 0, \strlen($needle)) === $needle));
    }
}
