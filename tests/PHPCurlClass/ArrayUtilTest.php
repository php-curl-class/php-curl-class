<?php declare(strict_types=1);

namespace CurlTest;

use Curl\ArrayUtil;
use Curl\CaseInsensitiveArray;

class ArrayUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testArrayAssociative()
    {
        $this->assertTrue(ArrayUtil::isArrayAssoc([
            'foo' => 'wibble',
            'bar' => 'wubble',
            'baz' => 'wobble',
        ]));
    }

    public function testArrayIndexed()
    {
        $this->assertFalse(ArrayUtil::isArrayAssoc([
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

    public function testArrayFlattenMultidimArray()
    {
        $data = array(
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => [
                'nested-key-1' => 'nested-value-1',
                'nested-key-2' => 'nested-value-2',
                'nested-key-3' => [
                    'nested-more-key-1' => 'nested-more-value-1',
                    'nested-more-key-2' => 'nested-more-value-2',
                ],
            ],
        );

        $this->assertEquals([
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3[nested-key-1]' => 'nested-value-1',
            'key-3[nested-key-2]' => 'nested-value-2',
            'key-3[nested-key-3][nested-more-key-1]' => 'nested-more-value-1',
            'key-3[nested-key-3][nested-more-key-2]' => 'nested-more-value-2',
        ], ArrayUtil::arrayFlattenMultidim($data));
    }

    public function testArrayFlattenMultidimOrdering()
    {
        $data = [
            'foo' => 'bar',
            'baz' => [
                'qux' => [
                ],
                'wibble' => 'wobble',
            ],
        ];

        $result = ArrayUtil::arrayFlattenMultidim($data);

        // Avoid using assertEquals() as it isn't strict about ordering:
        //   $this->assertEquals([
        //       'foo' => 'bar',
        //       'baz[qux]' => '',
        //       'baz[wibble]' => 'wobble',
        //   ], ArrayUtil::arrayFlattenMultidim($data));

        $result_keys = array_keys($result);
        $result_values = array_values($result);

        $this->assertEquals('foo', $result_keys[0]);
        $this->assertEquals('baz[qux]', $result_keys[1]);
        $this->assertEquals('baz[wibble]', $result_keys[2]);

        $this->assertEquals('bar', $result_values[0]);
        $this->assertEquals('', $result_values[1]);
        $this->assertEquals('wobble', $result_values[2]);
    }
}
