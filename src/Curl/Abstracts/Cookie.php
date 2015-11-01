<?php

namespace Curl\Abstracts;

abstract class Cookie {

    /**
     * @var int[] RFC2616 safe characters.
     * @see https://www.ietf.org/rfc/rfc2616.txt
     * @see sanitizeName()
     */
    private static $RFC2616 = array(
        '!' => 1, '#' => 1, '$' => 1, '%' => 1, '&' => 1, "'" => 1, '*' => 1,
        '+' => 1, '-' => 1, '.' => 1, '0' => 1, '1' => 1, '2' => 1, '3' => 1,
        '4' => 1, '5' => 1, '6' => 1, '7' => 1, '8' => 1, '9' => 1, 'A' => 1,
        'B' => 1, 'C' => 1, 'D' => 1, 'E' => 1, 'F' => 1, 'G' => 1, 'H' => 1,
        'I' => 1, 'J' => 1, 'K' => 1, 'L' => 1, 'M' => 1, 'N' => 1, 'O' => 1,
        'P' => 1, 'Q' => 1, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 1,
        'W' => 1, 'X' => 1, 'Y' => 1, 'Z' => 1, '^' => 1, '_' => 1, '`' => 1,
        'a' => 1, 'b' => 1, 'c' => 1, 'd' => 1, 'e' => 1, 'f' => 1, 'g' => 1,
        'h' => 1, 'i' => 1, 'j' => 1, 'k' => 1, 'l' => 1, 'm' => 1, 'n' => 1,
        'o' => 1, 'p' => 1, 'q' => 1, 'r' => 1, 's' => 1, 't' => 1, 'u' => 1,
        'v' => 1, 'w' => 1, 'x' => 1, 'y' => 1, 'z' => 1, '|' => 1, '~' => 1
    );

    /**
     * @var int[] RFC6265 safe characters.
     * @see https://tools.ietf.org/html/rfc6265
     * @see sanitizeValue()
     */
    private static $RFC6265 = array(
        // %x21
        '!' => 1,
        // %x23-2B
        '#' => 1, '$' => 1, '%' => 1, '&' => 1, "'" => 1, '(' => 1, ')' => 1,
        '*' => 1, '+' => 1,
        // %x2D-3A
        '-' => 1, '.' => 1, '/' => 1, '0' => 1, '1' => 1, '2' => 1, '3' => 1,
        '4' => 1, '5' => 1, '6' => 1, '7' => 1, '8' => 1, '9' => 1, ':' => 1,
        // %x3C-5B
        '<' => 1, '=' => 1, '>' => 1, '?' => 1, '@' => 1, 'A' => 1, 'B' => 1,
        'C' => 1, 'D' => 1, 'E' => 1, 'F' => 1, 'G' => 1, 'H' => 1, 'I' => 1,
        'J' => 1, 'K' => 1, 'L' => 1, 'M' => 1, 'N' => 1, 'O' => 1, 'P' => 1,
        'Q' => 1, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 1, 'W' => 1,
        'X' => 1, 'Y' => 1, 'Z' => 1, '[' => 1,
        // %x5D-7E
        ']' => 1, '^' => 1, '_' => 1, '`' => 1, 'a' => 1, 'b' => 1, 'c' => 1,
        'd' => 1, 'e' => 1, 'f' => 1, 'g' => 1, 'h' => 1, 'i' => 1, 'j' => 1,
        'k' => 1, 'l' => 1, 'm' => 1, 'n' => 1, 'o' => 1, 'p' => 1, 'q' => 1,
        'r' => 1, 's' => 1, 't' => 1, 'u' => 1, 'v' => 1, 'w' => 1, 'x' => 1,
        'y' => 1, 'z' => 1, '{' => 1, '|' => 1, '}' => 1, '~' => 1
    );

    /**
     * Sanitize
     *
     * Saitize a provided String using `rawurlencode()`, excluding the provided
     * Safe Characters.
     *
     * @param string[] $safe A collection of Safe Characters
     * @param string $string A string to be sanitized.
     *
     * @return string The sanitized String.
     *
     * @static
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    private static function __sanitize(Array $safe, $string) {
        $safe_string = '';
        foreach (str_split($string) as $char) {
            if (!isset($safe[$char])) {
                $char = rawurlencode($char);
            }
            $safe_string .= $char;
        }
        return (string) $safe_string;
    }

    /**
     * Sanitize Name
     *
     * Sanitize a provided string according to RFC2616 as a Cookie Name.
     *
     * @param string $string String to be sanitized.
     *
     * @return string Sanitized String.
     *
     * @static
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public static function sanitizeName($string) {
        return (string) self::__sanitize(self::$RFC2616, $string);
    }

    /**
     * Sanitize Value
     *
     * Sanitize the provided string according to RFC6265 as a Cookie Value.
     *
     * @param string $string String to be sanitized.
     *
     * @return string Sanitized String.
     *
     * @static
     * @access public
     *
     * @author Michael Mulligan <michael@bigroomstudios.com>
     */
    public static function sanitizeValue($string) {
        return (string) self::__sanitize(self::$RFC6265, $string);
    }
}