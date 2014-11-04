<?php

namespace Brick\DateTime\Tests;

use Brick\DateTime\Instant;
use Brick\DateTime\Interval;

class IntervalTest extends AbstractTestCase
{
    /**
     * @dataProvider providerContains
     *
     * @param integer $h1             The 1st interval's start hour.
     * @param integer $m1             The 1st interval's start minute.
     * @param integer $h2             The 1nd interval's end hour.
     * @param integer $m2             The 1nd interval's end minute.
     * @param integer $h3             The 2nd interval's start hour.
     * @param integer $m3             The 2nd interval's start minute.
     * @param integer $h4             The 2nd interval's end hour.
     * @param integer $m4             The 2nd interval's end minute.
     * @param integer $expectedResult The expected result.
     */
    public function testContains($h1, $m1, $h2, $m2, $h3, $m3, $h4, $m4, $expectedResult)
    {
        $interval1 = new Interval(Instant::of($h1 * 3600 + $m1 * 60), Instant::of($h2 * 3600 + $m2 * 60));
        $interval2 = new Interval(Instant::of($h3 * 3600 + $m3 * 60), Instant::of($h4 * 3600 + $m4 * 60));

        $this->assertSame($expectedResult, $interval1->contains($interval2));
    }

    /**
     * @return array
     */
    public function providerContains()
    {
        return [
            // [09:00 to 10:00) contains [09:00 to 10:00) = true
            [
                9, 0, 10, 0,
                9, 0, 10, 0,
                true
            ],

            // [09:00 to 10:00) contains [09:00 to 09:30) = true
            [
                9, 0, 10,  0,
                9, 0,  9, 30,
                true
            ],

            // [09:00 to 10:00) contains [09:30 to 10:00) = true
            [
                9, 0,  10, 0,
                9, 30, 10, 0,
                true
            ],

            // [09:00 to 10:00) contains [09:15 to 09:45) = true
            [
                9, 0,  10,  0,
                9, 15,  9, 45,
                true
            ],

            // [09:00 to 10:00) contains [09:00 to 09:00) = true
            [
                9, 0, 10, 0,
                9, 0,  9, 0,
                true
            ],

            // [09:00 to 10:00) contains [08:59 to 10:00) = false (otherStart before thisStart)
            [
                9, 0,  10, 0,
                8, 59, 10, 0,
                false
            ],

            // [09:00 to 10:00) contains [09:00 to 10:01) = false (otherEnd after thisEnd)
            [
                9, 0, 10, 0,
                9, 0, 10, 1,
                false
            ],

            // [09:00 to 10:00) contains [10:00 to 10:00) = false (otherStart equals thisEnd)
            [
                 9,  0, 10, 0,
                10,  0, 10, 0,
                false
            ],

            // [14:00 to 14:00) contains [14:00 to 14:00) = false (zero duration contains nothing)
            [
                14,  0, 14, 0,
                14,  0, 14, 0,
                false
            ]
        ];
    }

    /**
     * @dataProvider providerContainsInstant
     *
     * @param integer $h1             The interval's start hour.
     * @param integer $m1             The interval's start minute.
     * @param integer $h2             The interval's end hour.
     * @param integer $m2             The interval's end minute.
     * @param integer $h3             The hour of the test instant.
     * @param integer $m3             The minute of the test instant.
     * @param integer $expectedResult The expected result.
     */
    public function testContainsInstant($h1, $m1, $h2, $m2, $h3, $m3, $expectedResult)
    {
        $interval = new Interval(Instant::of($h1 * 3600 + $m1 * 60), Instant::of($h2 * 3600 + $m2 * 60));
        $instant = Instant::of($h3 * 3600 + $m3 * 60);

        $this->assertSame($expectedResult, $interval->containsInstant($instant));
    }

    /**
     * @return array
     */
    public function providerContainsInstant()
    {
        return [
            // [09:00 to 10:00) contains 08:59 = false (before start)
            [9, 0, 10, 0, 8, 59, false],

            // [09:00 to 10:00) contains 09:00 = true
            [9, 0, 10, 0, 9, 0, true],

            // [09:00 to 10:00) contains 09:59 = true
            [9, 0, 10, 0, 9, 59, true],

            // [09:00 to 10:00) contains 10:00 = false (equals end)
            [9, 0, 10, 0, 10, 0, false],

            // [09:00 to 10:00) contains 10:01 = false (after end)
            [9, 0, 10, 0, 10, 1, false],

            // [14:00 to 14:00) contains 14:00 = false (zero duration contains nothing)
            [14, 0, 14, 0, 14, 0, false]
        ];
    }
}
