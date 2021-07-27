<?php declare(strict_types=1);

namespace Curl;

use Curl\StringUtil;

class Url
{
    private $baseUrl = null;
    private $relativeUrl = null;

    public function __construct($base_url, $relative_url = null)
    {
        $this->baseUrl = $base_url;
        $this->relativeUrl = $relative_url;
    }

    public function __toString()
    {
        return $this->absolutizeUrl();
    }

    /**
     * Remove dot segments.
     *
     * Interpret and remove the special "." and ".." path segments from a referenced path.
     */
    public static function removeDotSegments($input)
    {
        // 1.  The input buffer is initialized with the now-appended path
        //     components and the output buffer is initialized to the empty
        //     string.
        $output = '';

        // 2.  While the input buffer is not empty, loop as follows:
        while (!empty($input)) {
            // A.  If the input buffer begins with a prefix of "../" or "./",
            //     then remove that prefix from the input buffer; otherwise,
            if (StringUtil::startsWith($input, '../')) {
                $input = substr($input, 3);
            } elseif (StringUtil::startsWith($input, './')) {
                $input = substr($input, 2);

            // B.  if the input buffer begins with a prefix of "/./" or "/.",
            //     where "." is a complete path segment, then replace that
            //     prefix with "/" in the input buffer; otherwise,
            } elseif (StringUtil::startsWith($input, '/./')) {
                $input = substr($input, 2);
            } elseif ($input === '/.') {
                $input = '/';

            // C.  if the input buffer begins with a prefix of "/../" or "/..",
            //     where ".." is a complete path segment, then replace that
            //     prefix with "/" in the input buffer and remove the last
            //     segment and its preceding "/" (if any) from the output
            //     buffer; otherwise,
            } elseif (StringUtil::startsWith($input, '/../')) {
                $input = substr($input, 3);
                $output = substr_replace($output, '', StringUtil::reversePosition($output, '/'));
            } elseif ($input === '/..') {
                $input = '/';
                $output = substr_replace($output, '', StringUtil::reversePosition($output, '/'));

            // D.  if the input buffer consists only of "." or "..", then remove
            //     that from the input buffer; otherwise,
            } elseif ($input === '.' || $input === '..') {
                $input = '';

            // E.  move the first path segment in the input buffer to the end of
            //     the output buffer, including the initial "/" character (if
            //     any) and any subsequent characters up to, but not including,
            //     the next "/" character or the end of the input buffer.
            } elseif (!(($pos = StringUtil::position($input, '/', 1)) === false)) {
                $output .= substr($input, 0, $pos);
                $input = substr_replace($input, '', 0, $pos);
            } else {
                $output .= $input;
                $input = '';
            }
        }

        // 3.  Finally, the output buffer is returned as the result of
        //     remove_dot_segments.
        return $output . $input;
    }

    /**
     * Build Url
     *
     * @access public
     * @param  $url
     * @param  $mixed_data
     *
     * @return string
     */
    public static function buildUrl($url, $mixed_data = '')
    {
        $query_string = '';
        if (!empty($mixed_data)) {
            $query_mark = strpos($url, '?') > 0 ? '&' : '?';
            if (is_string($mixed_data)) {
                $query_string .= $query_mark . $mixed_data;
            } elseif (is_array($mixed_data)) {
                $query_string .= $query_mark . http_build_query($mixed_data, '', '&');
            }
        }
        return $url . $query_string;
    }

    /**
     * Absolutize url.
     *
     * Combine the base and relative url into an absolute url.
     */
    private function absolutizeUrl()
    {
        $b = $this->parseUrl($this->baseUrl);
        if (!isset($b['path'])) {
            $b['path'] = '/';
        }
        if ($this->relativeUrl === null) {
            return $this->unparseUrl($b);
        }
        $r = $this->parseUrl($this->relativeUrl);
        $r['authorized'] = isset($r['scheme']) || isset($r['host']) || isset($r['port'])
            || isset($r['user']) || isset($r['pass']);
        $target = [];
        if (isset($r['scheme'])) {
            $target['scheme'] = $r['scheme'];
            $target['host'] = isset($r['host']) ? $r['host'] : null;
            $target['port'] = isset($r['port']) ? $r['port'] : null;
            $target['user'] = isset($r['user']) ? $r['user'] : null;
            $target['pass'] = isset($r['pass']) ? $r['pass'] : null;
            $target['path'] = isset($r['path']) ? self::removeDotSegments($r['path']) : null;
            $target['query'] = isset($r['query']) ? $r['query'] : null;
        } else {
            $target['scheme'] = isset($b['scheme']) ? $b['scheme'] : null;
            if ($r['authorized']) {
                $target['host'] = isset($r['host']) ? $r['host'] : null;
                $target['port'] = isset($r['port']) ? $r['port'] : null;
                $target['user'] = isset($r['user']) ? $r['user'] : null;
                $target['pass'] = isset($r['pass']) ? $r['pass'] : null;
                $target['path'] = isset($r['path']) ? self::removeDotSegments($r['path']) : null;
                $target['query'] = isset($r['query']) ? $r['query'] : null;
            } else {
                $target['host'] = isset($b['host']) ? $b['host'] : null;
                $target['port'] = isset($b['port']) ? $b['port'] : null;
                $target['user'] = isset($b['user']) ? $b['user'] : null;
                $target['pass'] = isset($b['pass']) ? $b['pass'] : null;
                if (!isset($r['path']) || $r['path'] === '') {
                    $target['path'] = $b['path'];
                    $target['query'] = isset($r['query']) ? $r['query'] : (isset($b['query']) ? $b['query'] : null);
                } else {
                    if (StringUtil::startsWith($r['path'], '/')) {
                        $target['path'] = self::removeDotSegments($r['path']);
                    } else {
                        $base = StringUtil::characterReversePosition($b['path'], '/', true);
                        if ($base === false) {
                            $base = '';
                        }
                        $target['path'] = self::removeDotSegments($base . '/' . $r['path']);
                    }
                    $target['query'] = isset($r['query']) ? $r['query'] : null;
                }
            }
        }
        if ($this->relativeUrl === '') {
            $target['fragment'] = isset($b['fragment']) ? $b['fragment'] : null;
        } else {
            $target['fragment'] = isset($r['fragment']) ? $r['fragment'] : null;
        }
        $absolutized_url = $this->unparseUrl($target);
        return $absolutized_url;
    }

    /**
     * Parse url.
     *
     * Parse url into components of a URI as specified by RFC 3986.
     */
    private function parseUrl($url)
    {
        // RFC 3986 - Parsing a URI Reference with a Regular Expression.
        //       ^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
        //        12            3  4          5       6  7        8 9
        //
        // "http://www.ics.uci.edu/pub/ietf/uri/#Related"
        // $1 = http: (scheme)
        // $2 = http (scheme)
        // $3 = //www.ics.uci.edu (ignore)
        // $4 = www.ics.uci.edu (authority)
        // $5 = /pub/ietf/uri/ (path)
        // $6 = <undefined> (ignore)
        // $7 = <undefined> (query)
        // $8 = #Related (ignore)
        // $9 = Related (fragment)
        preg_match('/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/', (string) $url, $output_array);

        $parts = [];
        if (isset($output_array['1']) && $output_array['1'] !== '') {
            $parts['scheme'] = $output_array['1'];
        }
        if (isset($output_array['2']) && $output_array['2'] !== '') {
            $parts['scheme'] = $output_array['2'];
        }
        if (isset($output_array['4']) && $output_array['4'] !== '') {
            // authority   = [ userinfo "@" ] host [ ":" port ]
            $parts['host'] = $output_array['4'];
            if (strpos($parts['host'], ':') !== false) {
                $host_parts = explode(':', $output_array['4']);
                $parts['port'] = array_pop($host_parts);
                $parts['host'] = implode(':', $host_parts);
                if (strpos($parts['host'], '@') !== false) {
                    $host_parts = explode('@', $parts['host']);
                    $parts['host'] = array_pop($host_parts);
                    $parts['user'] = implode('@', $host_parts);
                    if (strpos($parts['user'], ':') !== false) {
                        $user_parts = explode(':', $parts['user'], 2);
                        $parts['user'] = array_shift($user_parts);
                        $parts['pass'] = implode(':', $user_parts);
                    }
                }
            }
        }
        if (isset($output_array['5']) && $output_array['5'] !== '') {
            $parts['path'] = $this->percentEncodeChars($output_array['5']);
        }
        if (isset($output_array['7']) && $output_array['7'] !== '') {
            $parts['query'] = $output_array['7'];
        }
        if (isset($output_array['9']) && $output_array['9'] !== '') {
            $parts['fragment'] = $output_array['9'];
        }
        return $parts;
    }

    /**
     * Percent-encode characters.
     *
     * Percent-encode characters to represent a data octet in a component when
     * that octet's corresponding character is outside the allowed set.
     */
    private function percentEncodeChars($chars)
    {
        // ALPHA         = A-Z / a-z
        $alpha = 'A-Za-z';

        // DIGIT         = 0-9
        $digit = '0-9';

        // unreserved    = ALPHA / DIGIT / "-" / "." / "_" / "~"
        $unreserved = $alpha . $digit . preg_quote('-._~');

        // sub-delims    = "!" / "$" / "&" / "'" / "(" / ")"
        //               / "*" / "+" / "," / ";" / "=" / "#"
        $sub_delims = preg_quote('!$&\'()*+,;=#');

        // HEXDIG         =  DIGIT / "A" / "B" / "C" / "D" / "E" / "F"
        $hexdig = $digit . 'A-F';
        // "The uppercase hexadecimal digits 'A' through 'F' are equivalent to
        // the lowercase digits 'a' through 'f', respectively."
        $hexdig .= 'a-f';

        $pattern = '/(?:[^' . $unreserved . $sub_delims . preg_quote(':@%/?', '/') . ']++|%(?![' . $hexdig . ']{2}))/';
        $percent_encoded_chars = preg_replace_callback(
            $pattern,
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $chars
        );
        return $percent_encoded_chars;
    }

    /**
     * Unparse url.
     *
     * Combine url components into a url.
     */
    private function unparseUrl($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme'])   ?       $parsed_url['scheme'] . '://' : '';
        $user     = isset($parsed_url['user'])     ?       $parsed_url['user']           : '';
        $pass     = isset($parsed_url['pass'])     ? ':' . $parsed_url['pass']           : '';
        $pass     = ($user || $pass)               ?       $pass . '@'                   : '';
        $host     = isset($parsed_url['host'])     ?       $parsed_url['host']           : '';
        $port     = isset($parsed_url['port'])     ? ':' . $parsed_url['port']           : '';
        $path     = isset($parsed_url['path'])     ?       $parsed_url['path']           : '';
        $query    = isset($parsed_url['query'])    ? '?' . $parsed_url['query']          : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment']       : '';
        $unparsed_url =  $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
        return $unparsed_url;
    }
}
