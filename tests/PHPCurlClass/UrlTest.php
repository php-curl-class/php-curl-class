<?php

namespace CurlTest;

use \Curl\Url;

class UrlTest extends \PHPUnit\Framework\TestCase
{
    public function testUrlPaths()
    {
        $tests = array(
            array(
                'args' => array(
                    'http://www.example.com/',
                    '/foo',
                ),
                'expected' => 'http://www.example.com/foo',
            ),
            array(
                'args' => array(
                    'http://www.example.com/',
                    '/foo/',
                ),
                'expected' => 'http://www.example.com/foo/',
            ),
            array(
                'args' => array(
                    'http://www.example.com/',
                    '/dir/page.html',
                ),
                'expected' => 'http://www.example.com/dir/page.html',
            ),
            array(
                'args' => array(
                    'http://www.example.com/dir1/page2.html',
                    '/dir/page.html',
                ),
                'expected' => 'http://www.example.com/dir/page.html',
            ),
            array(
                'args' => array(
                    'http://www.example.com/dir1/page2.html',
                    'dir/page.html',
                ),
                'expected' => 'http://www.example.com/dir1/dir/page.html',
            ),
            array(
                'args' => array(
                    'http://www.example.com/dir1/dir3/page.html',
                    '../dir/page.html',
                ),
                'expected' => 'http://www.example.com/dir1/dir/page.html',
            ),
        );
        foreach ($tests as $test) {
            $reflector = new \ReflectionClass('\Curl\Url');
            $url = $reflector->newInstanceArgs($test['args']);
            $actual_url = (string)$url;
            $this->assertEquals($test['expected'], $actual_url);
        }
    }

    public function testUrlInstances()
    {
        $a = new Url('https://developer.mozilla.org', '/');
        $this->assertEquals('https://developer.mozilla.org/', $a);

        $b = new Url('https://developer.mozilla.org');
        $this->assertEquals('https://developer.mozilla.org/', $b);

        $c = new Url($b, 'en-US/docs');
        $this->assertEquals('https://developer.mozilla.org/en-US/docs', $c);

        $d = new Url($b, '/en-US/docs');
        $this->assertEquals('https://developer.mozilla.org/en-US/docs', $d);

        $f = new Url($d, '/en-US/docs');
        $this->assertEquals('https://developer.mozilla.org/en-US/docs', $f);

        $g = new Url('https://developer.mozilla.org/fr-FR/toto', '/en-US/docs');
        $this->assertEquals('https://developer.mozilla.org/en-US/docs', $g);

        $h = new Url($a, '/en-US/docs');
        $this->assertEquals('https://developer.mozilla.org/en-US/docs', $h);

        $k = new Url('https://developers.mozilla.com', 'http://www.example.com');
        $this->assertEquals('http://www.example.com/', $k);

        $l = new Url($b, 'http://www.example.com');
        $this->assertEquals('http://www.example.com/', $l);
    }

    public function testRemoveDotSegments()
    {
        // TODO: Add tests using "Normal Examples" and "Abnormal Examples" from RFC 3986.
        $tests = array(
            array(
                'path' => '/a/b/c/./../../g',
                'expected' => '/a/g',
            ),
            array(
                'path' => 'mid/content=5/../6',
                'expected' => 'mid/6',
            ),
        );
        foreach ($tests as $test) {
            $actual_path = Url::removeDotSegments($test['path']);
            $this->assertEquals($test['expected'], $actual_path);
        }
    }

    public function testCyrillicChars()
    {
        $path_part = 'Банан-комнатный-саженцы-банана';
        $original_url = 'https://www.example.com/path/' . $path_part               . '/page.html';
        $expected_url = 'https://www.example.com/path/' . rawurlencode($path_part) . '/page.html';
        $url = new Url($original_url);
        $this->assertEquals($expected_url, $url);
    }
}
