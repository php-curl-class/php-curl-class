<?php

namespace Curl;

use Curl\StrUtil;

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
            if (StrUtil::startsWith($input, '../')) {
                $input = substr($input, 3);
            } elseif (StrUtil::startsWith($input, './')) {
                $input = substr($input, 2);

            // B.  if the input buffer begins with a prefix of "/./" or "/.",
            //     where "." is a complete path segment, then replace that
            //     prefix with "/" in the input buffer; otherwise,
            } elseif (StrUtil::startsWith($input, '/./')) {
                $input = substr($input, 2);
            } elseif ($input === '/.') {
                $input = '/';

            // C.  if the input buffer begins with a prefix of "/../" or "/..",
            //     where ".." is a complete path segment, then replace that
            //     prefix with "/" in the input buffer and remove the last
            //     segment and its preceding "/" (if any) from the output
            //     buffer; otherwise,
            } elseif (StrUtil::startsWith($input, '/../')) {
                $input = substr($input, 3);
                $output = substr_replace($output, '', mb_strrpos($output, '/'));
            } elseif ($input === '/..') {
                $input = '/';
                $output = substr_replace($output, '', mb_strrpos($output, '/'));

            // D.  if the input buffer consists only of "." or "..", then remove
            //     that from the input buffer; otherwise,
            } elseif ($input === '.' || $input === '..') {
                $input = '';

            // E.  move the first path segment in the input buffer to the end of
            //     the output buffer, including the initial "/" character (if
            //     any) and any subsequent characters up to, but not including,
            //     the next "/" character or the end of the input buffer.
            } elseif (!(($pos = mb_strpos($input, '/', 1)) === false)) {
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
     * Absolutize url.
     *
     * Combine the base and relative url into an absolute url.
     */
    private function absolutizeUrl()
    {
        $b = $this->parseUrl($this->baseUrl);

        if (!($this->relativeUrl === null)) {
            $r = $this->parseUrl($this->relativeUrl);

            // Copy relative parts to base url.
            if (isset($r['scheme'])) {
                $b['scheme'] = $r['scheme'];
            }
            if (isset($r['host'])) {
                $b['host'] = $r['host'];
            }
            if (isset($r['port'])) {
                $b['port'] = $r['port'];
            }
            if (isset($r['user'])) {
                $b['user'] = $r['user'];
            }
            if (isset($r['pass'])) {
                $b['pass'] = $r['pass'];
            }

            if (!isset($r['path']) || $r['path'] === '') {
                $r['path'] = '/';
            }
            // Merge relative url with base when relative url's path doesn't start with a slash.
            if (!(StrUtil::startsWith($r['path'], '/'))) {
                $base = mb_strrchr($b['path'], '/', true);
                if ($base === false) {
                    $base = '';
                }
                $r['path'] = $base . '/' . $r['path'];
            }
            $b['path'] = $r['path'];
            $b['path'] = $this->removeDotSegments($b['path']);

            if (isset($r['query'])) {
                $b['query'] = $r['query'];
            }
            if (isset($r['fragment'])) {
                $b['fragment'] = $r['fragment'];
            }
        }

        if (!isset($b['path'])) {
            $b['path'] = '/';
        }

        $absolutized_url = $this->unparseUrl($b);
        return $absolutized_url;
    }

    /**
     * Parse url.
     *
     * Parse url into components of a URI as specified by RFC 3986.
     */
    private function parseUrl($url)
    {
        return parse_url($url);
    }

    /**
     * Unparse url.
     *
     * Combine url components into a url.
     */
    private function unparseUrl($parsed_url) {
        $scheme   = isset($parsed_url['scheme'])   ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host'])     ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port'])     ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user'])     ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass'])     ? ':' . $parsed_url['pass'] : '';
        $pass     = ($user || $pass)               ? $pass . '@' : '';
        $path     = isset($parsed_url['path'])     ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query'])    ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        $unparsed_url =  $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
        return $unparsed_url;
    }
}
