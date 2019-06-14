<?php

namespace CurlTest;

use \Curl\CaseInsensitiveArray;
use \Curl\Curl;
use \Helper\Test;
use \Helper\User;

class CurlTest extends \PHPUnit\Framework\TestCase
{
    public function testExtensionsLoaded()
    {
        $this->assertTrue(extension_loaded('curl'));
        $this->assertTrue(extension_loaded('gd'));
        $this->assertTrue(extension_loaded('mbstring'));
    }

    public function testArrayAssociative()
    {
        $this->assertTrue(\Curl\ArrayUtil::isArrayAssoc(array(
            'foo' => 'wibble',
            'bar' => 'wubble',
            'baz' => 'wobble',
        )));
    }

    public function testArrayIndexed()
    {
        $this->assertFalse(\Curl\ArrayUtil::isArrayAssoc(array(
            'wibble',
            'wubble',
            'wobble',
        )));
    }

    public function testCaseInsensitiveArrayGet()
    {
        $array = new CaseInsensitiveArray();
        $this->assertTrue(is_object($array));
        $this->assertCount(0, $array);
        $this->assertNull($array[(string)rand()]);

        $array['foo'] = 'bar';
        $this->assertNotEmpty($array);
        $this->assertCount(1, $array);
    }

    public function testCaseInsensitiveArraySet()
    {
        $array = new CaseInsensitiveArray();
        foreach (array('FOO', 'FOo', 'Foo', 'fOO', 'fOo', 'foO', 'foo') as $key) {
            $value = mt_rand();
            $array[$key] = $value;
            $this->assertCount(1, $array);
            $this->assertEquals($value, $array['FOO']);
            $this->assertEquals($value, $array['FOo']);
            $this->assertEquals($value, $array['Foo']);
            $this->assertEquals($value, $array['fOO']);
            $this->assertEquals($value, $array['fOo']);
            $this->assertEquals($value, $array['foO']);
            $this->assertEquals($value, $array['foo']);
        }

        $array['baz'] = 'qux';
        $this->assertCount(2, $array);
    }

    public function testBuildPostDataArgSeparator()
    {
        $data = array(
            'foo' => 'Hello',
            'bar' => 'World',
        );

        foreach (array(false, '&amp;', '&') as $arg_separator) {
            if ($arg_separator) {
                ini_set('arg_separator.output', $arg_separator);
            }
            $curl = new Curl();
            $this->assertEquals('foo=Hello&bar=World', $curl->buildPostData($data));
        }
    }

    public function testUserAgent()
    {
        $php_version = preg_replace('/([\.\+\?\*\(\)\[\]\^\$\/])/', '\\\\\1', 'PHP/' . PHP_VERSION);
        $curl_version = curl_version();
        $curl_version = 'curl\/' . $curl_version['version'];

        $test = new Test();
        $user_agent = $test->server('server', 'GET', array('key' => 'HTTP_USER_AGENT'));
        $this->assertRegExp('/' . $php_version . '/', $user_agent);
        $this->assertRegExp('/' . $curl_version . '/', $user_agent);
    }

    public function testGet()
    {
        $test = new Test();
        $this->assertEquals('GET', $test->server('request_method', 'GET'));
    }
}
