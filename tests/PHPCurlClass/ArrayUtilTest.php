<?php declare(strict_types=1);

namespace CurlTest;

use Curl\ArrayUtil;
use Curl\CaseInsensitiveArray;

class ArrayUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testArrayAssociative()
    {
        $this->assertTrue(\Curl\ArrayUtil::isArrayAssoc([
            'foo' => 'wibble',
            'bar' => 'wubble',
            'baz' => 'wobble',
        ]));
    }

    public function testArrayIndexed()
    {
        $this->assertFalse(\Curl\ArrayUtil::isArrayAssoc([
            'wibble',
            'wubble',
            'wobble',
        ]));
    }

    public function testCaseInsensitiveArrayIsArrayAssoc()
    {
        $array = new CaseInsensitiveArray();
        $this->assertTrue(ArrayUtil::isArrayAssoc($array));
    }
}
