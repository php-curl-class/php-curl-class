<?php

declare(strict_types=1);

namespace Curl;

class TimeUtil
{
    /**
     * Get the microtime (in microseconds) at which to sleep until.
     *
     * @param  float $start_time       The start time in seconds with microsecond precision.
     * @param  int   $interval_seconds The interval in seconds.
     * @return float The microtime (in microseconds) at which to sleep until.
     */
    public static function getSleepUntilMicrotime(
        float $start_time,
        int $interval_seconds,
    ): float {
        $result = $start_time + (float)$interval_seconds;
        return $result;
    }

    /**
     * Get the number of seconds to sleep until the specified microtime.
     *
     * @param  float $sleep_until_microtime The microtime (in microseconds) at which to sleep until.
     * @param  float $current_microtime     The current microtime (in microseconds).
     * @return float The number of seconds to sleep.
     */
    public static function getSleepSecondsUntilMicrotime(
        float $sleep_until_microtime,
        float $current_microtime,
    ): float {
        $result = $sleep_until_microtime - $current_microtime;

        // Always round up with microsecond precision to avoid sleeping less
        // than required.
        $rounded_up_result = ceil($result * (float)1_000_000) / (float)1_000_000;
        return $rounded_up_result;
    }

    /**
     * Get the whole seconds and microseconds remainder from the given sleep
     * seconds.
     *
     * @param  float $sleep_seconds The number of seconds to sleep.
     * @return array An array containing the whole seconds and microseconds remainder.
     */
    public static function getWholeAndRemainderSeconds(
        float $sleep_seconds,
    ): array {
        $micros = (int) ceil($sleep_seconds * (float)1_000_000);
        $whole_seconds = intdiv($micros, 1_000_000);
        $microseconds_remainder = $micros % 1_000_000;
        return [
            $whole_seconds,
            $microseconds_remainder,
        ];
    }
}
