<?php

declare(strict_types=1);

namespace Helper;

use Curl\MultiCurl;

/**
 * Skip rate limit waits instead of sleeping through them.
 *
 * Time still advances in real time while requests are in flight, so tests keep
 * asserting real request timing; only the idle waits between rate limit
 * intervals are skipped.
 */
class VirtualClockMultiCurl extends MultiCurl
{
    private $skippedSeconds = 0.0;

    #[\Override]
    public function getCurrentMicrotime(): float
    {
        return microtime(true) + $this->skippedSeconds;
    }

    #[\Override]
    protected function sleepSeconds(float $sleep_seconds): void
    {
        if ($sleep_seconds > 0) {
            $this->skippedSeconds += $sleep_seconds;
        }
    }
}
