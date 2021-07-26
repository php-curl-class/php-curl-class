<?php declare(strict_types=1);

namespace CurlTest;

use Curl\ArrayUtil;
use Curl\CaseInsensitiveArray;

class ArrayUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testCaseInsensitiveArrayIsArrayAssoc()
    {
        $array = new CaseInsensitiveArray();
        $this->assertTrue(ArrayUtil::isArrayAssoc($array));
    }
}
