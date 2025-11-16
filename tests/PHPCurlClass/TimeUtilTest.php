<?php

declare(strict_types=1);

namespace CurlTest;

use Curl\TimeUtil;

class TimeUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSleepUntilMicrotime()
    {
        // Use a fixed start time.
        // [...]ime = microtime(true);
        $start_time = (float)1_750_000_000.123456;

        // Use a fixed current time that occurs after the start time.
        // [...]_microtime = (float)1_750_000_045.000001;
        $current_microtime = (float)1_750_000_044.999999;

        $interval_seconds = 60;
        $sleep_until_microtime = TimeUtil::getSleepUntilMicrotime(
            $start_time,
            $interval_seconds,
        );

        $this->assertEquals(
            (float)1_750_000_060.123456,
            $sleep_until_microtime,
        );
    }

    public function testGetSleepSecondsUntilMicrotime()
    {
        $sleep_until_microtime = (float)1_750_000_060.123456;
        $current_microtime = (float)1_750_000_044.999999;

        $sleep_seconds = TimeUtil::getSleepSecondsUntilMicrotime(
            $sleep_until_microtime,
            $current_microtime,
        );

        $this->assertEquals(
            (float)15.123457,
            $sleep_seconds,
        );
    }

    public function testGetWholeAndRemainderSeconds()
    {
        $sleep_seconds = (float)15.123457;

        list($whole_seconds, $microseconds_remainder) = TimeUtil::getWholeAndRemainderSeconds(
            $sleep_seconds,
        );

        $this->assertEquals(15, $whole_seconds);
        $this->assertEquals(123457, $microseconds_remainder);
    }
}
