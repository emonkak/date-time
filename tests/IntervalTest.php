<?php

namespace Brick\DateTime\Tests;

use Brick\DateTime\Instant;
use Brick\DateTime\Interval;

class IntervalTest extends AbstractTestCase
{
    /**
     * @dataProvider providerAbuts
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
    public function testAbuts($h1, $m1, $h2, $m2, $h3, $m3, $h4, $m4, $expectedResult)
    {
        $interval1 = new Interval(Instant::of($h1 * 3600 + $m1 * 60), Instant::of($h2 * 3600 + $m2 * 60));
        $interval2 = new Interval(Instant::of($h3 * 3600 + $m3 * 60), Instant::of($h4 * 3600 + $m4 * 60));

        $this->assertSame($expectedResult, $interval1->abuts($interval2));
    }

    /**
     * @return array
     */
    public function providerAbuts()
    {
        return [
            // [09:00 to 10:00) abuts [08:00 to 08:30) = false (completely before)
            [
                9, 0, 10,  0,
                8, 0,  8, 30,
                false
            ],

            // [09:00 to 10:00) abuts [08:00 to 09:00) = true
            [
                9, 0, 10, 0,
                8, 0,  9, 0,
                true
            ],

            // [09:00 to 10:00) abuts [08:00 to 09:01) = false (overlaps)
            [
                9, 0, 10, 0,
                8, 0,  9, 1,
                false
            ],

            // [09:00 to 10:00) abuts [09:00 to 09:00) = true
            [
                9, 0, 10, 0,
                9, 0,  9, 0,
                true
            ],

            // [09:00 to 10:00) abuts [09:00 to 09:01) = false (overlaps)
            [
                9, 0, 10, 0,
                9, 0,  9, 1,
                false
            ],

            // [09:00 to 10:00) abuts [10:00 to 10:00) = true
            [
                 9, 0, 10, 0,
                10, 0, 10, 0,
                true
            ],

            // [09:00 to 10:00) abuts [10:00 to 10:30) = true
            [
                 9, 0, 10,  0,
                10, 0, 10, 30,
                true
            ],

            // [09:00 to 10:00) abuts [10:30 to 11:00) = false (completely after)
            [
                 9,  0, 10, 0,
                10, 30, 11, 0,
                false
            ],

            // [14:00 to 14:00) abuts [14:00 to 14:00) = true
            [
                14, 0, 14, 0,
                14, 0, 14, 0,
                true
            ],

            // [14:00 to 14:00) abuts [14:00 to 15:00) = true
            [
                14, 0, 14, 0,
                14, 0, 15, 0,
                true
            ],

            // [14:00 to 14:00) abuts [13:00 to 14:00) = true
            [
                14, 0, 14, 0,
                13, 0, 14, 0,
                true
            ]
        ];
    }

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

    /**
     * @dataProvider providerOverlaps
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
    public function testOverlaps($h1, $m1, $h2, $m2, $h3, $m3, $h4, $m4, $expectedResult)
    {
        $interval1 = new Interval(Instant::of($h1 * 3600 + $m1 * 60), Instant::of($h2 * 3600 + $m2 * 60));
        $interval2 = new Interval(Instant::of($h3 * 3600 + $m3 * 60), Instant::of($h4 * 3600 + $m4 * 60));

        $this->assertSame($expectedResult, $interval1->overlaps($interval2));
    }

    /**
     * @return array
     */
    public function providerOverlaps()
    {
        return [
            // [09:00 to 10:00) overlaps [08:00 to 08:30) = false (completely before)
            [
                9, 0, 10,  0,
                8, 0,  8, 30,
                false
            ],

            // [09:00 to 10:00) contains [08:00 to 09:00) = false (abuts before)
            [
                9, 0, 10, 0,
                8, 0,  9, 0,
                false
            ],

            // [09:00 to 10:00) overlaps [08:00 to 09:30) = true
            [
                9, 0, 10,  0,
                8, 0,  9, 30,
                true
            ],

            // [09:00 to 10:00) overlaps [08:00 to 10:00) = true
            [
                9, 0, 10, 0,
                8, 0, 10, 0,
                true
            ],

            // [09:00 to 10:00) overlaps [08:00 to 11:00) = true
            [
                9, 0, 10, 0,
                8, 0, 11, 0,
                true
            ],

            // [09:00 to 10:00) overlaps [09:00 to 09:00) = false (abuts before)
            [
                9, 0, 10, 0,
                9, 0,  9, 0,
                false
            ],

            // [09:00 to 10:00) overlaps [09:00 to 09:30) = true
            [
                9, 0, 10,  0,
                9, 0,  9, 30,
                true
            ],

            // [09:00 to 10:00) overlaps [09:00 to 10:00) = true
            [
                9, 0, 10, 0,
                9, 0, 10, 0,
                true
            ],

            // [09:00 to 10:00) overlaps [09:00 to 11:00) = true
            [
                9, 0, 10, 0,
                9, 0, 11, 0,
                true
            ],

            // [09:00 to 10:00) overlaps [09:30 to 09:30) = true
            [
                9,  0, 10,  0,
                9, 30,  9, 30,
                true
            ],

            // [09:00 to 10:00) overlaps [09:30 to 10:00) = true
            [
                9,  0, 10, 0,
                9, 30, 10, 0,
                true
            ],

            // [09:00 to 10:00) overlaps [09:30 to 11:00) = true
            [
                9,  0, 10, 0,
                9, 30, 11, 0,
                true
            ],

            // [09:00 to 10:00) overlaps [10:00 to 10:00) = false (abuts after)
            [
                 9, 0, 10, 0,
                10, 0, 10, 0,
                false
            ],

            // [09:00 to 10:00) overlaps [10:00 to 11:00) = false (abuts after)
            [
                 9, 0, 10, 0,
                10, 0, 11, 0,
                false
            ],

            // [09:00 to 10:00) overlaps [10:30 to 11:00) = false (completely after)
            [
                 9,  0, 10, 0,
                10, 30, 11, 0,
                false
            ],

            // [14:00 to 14:00) overlaps [14:00 to 14:00) = false (abuts before and after)
            [
                14, 0, 14, 0,
                14, 0, 14, 0,
                false
            ],

            // [14:00 to 14:00) overlaps [13:00 to 15:00) = true
            [
                14, 0, 14, 0,
                13, 0, 15, 0,
                true
            ]
        ];
    }
}
