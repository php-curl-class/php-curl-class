<?php

namespace CurlTest;

use Curl\Url;

class UrlTest extends \PHPUnit\Framework\TestCase
{
    public function testUrlPaths()
    {
        $urls_file = gzopen(dirname(__FILE__) . '/urls.csv.gz', 'r');
        fgetcsv($urls_file); // header
        while (($test = fgetcsv($urls_file)) !== false) {
            $url = new Url($test[0], $test[1]);
            $actual_url = (string)$url;
            $expected_url = $test[2];
            $this->assertEquals($expected_url, $actual_url, "Joint URLs: '{$test[0]}', '{$test[1]}'");
        }
        fclose($urls_file);
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
        $this->assertEquals('http://www.example.com', $k);

        $l = new Url($b, 'http://www.example.com');
        $this->assertEquals('http://www.example.com', $l);
    }

    public function testRemoveDotSegments()
    {
        // TODO: Add tests using "Normal Examples" and "Abnormal Examples" from RFC 3986.
        $tests = [
            [
                'path' => '/a/b/c/./../../g',
                'expected' => '/a/g',
            ],
            [
                'path' => 'mid/content=5/../6',
                'expected' => 'mid/6',
            ],
        ];
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

    public function testParseUrlSyntaxComponents()
    {
        // RFC 3986 - Syntax Components.
        //   The following are two example URIs and their component parts:
        //
        //         foo://example.com:8042/over/there?name=ferret#nose
        //         \_/   \______________/\_________/ \_________/ \__/
        //          |           |            |            |        |
        //       scheme     authority       path        query   fragment
        $input_url = 'foo://example.com:8042/over/there?name=ferret#nose';
        $expected_parts = [
            'scheme' => 'foo',
            'host' => 'example.com',
            'port' => '8042',
            'path' => '/over/there',
            'query' => 'name=ferret',
            'fragment' => 'nose',
        ];

        $this->assertEquals($expected_parts, parse_url($input_url));

        $reflector = new \ReflectionClass('Curl\Url');
        $reflection_method = $reflector->getMethod('parseUrl');
        $reflection_method->setAccessible(true);

        $url = new Url(null);
        $result = $reflection_method->invoke($url, $input_url);
        $this->assertEquals($expected_parts, $result);
    }

    public function testParseUrlExample()
    {
        $input_url = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $expected_parts = [
            'scheme' => 'http',
            'host' => 'hostname',
            'port' => '9090',
            'user' => 'username',
            'pass' => 'password',
            'path' => '/path',
            'query' => 'arg=value',
            'fragment' => 'anchor',
        ];

        $this->assertEquals($expected_parts, parse_url($input_url));

        $reflector = new \ReflectionClass('Curl\Url');
        $reflection_method = $reflector->getMethod('parseUrl');
        $reflection_method->setAccessible(true);

        $url = new Url(null);
        $result = $reflection_method->invoke($url, $input_url);
        $this->assertEquals($expected_parts, $result);
    }

    public function testIpv6NoPort()
    {
        $expected_url = 'http://[::1]/test';
        $actual_url = new Url($expected_url);
        $this->assertEquals($expected_url, $actual_url);
    }

    public function testIpv6Port()
    {
        $expected_url = 'http://[::1]:80/test';
        $actual_url = new Url($expected_url);
        $this->assertEquals($expected_url, $actual_url);
    }
}
