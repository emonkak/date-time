<?php

namespace Brick\DateTime\Tests;

use Brick\DateTime\Instant;
use Brick\DateTime\Interval;

class IntervalTest extends AbstractTestCase
{
    /**
     * @dataProvider providerGap
     *
     * @param array      $first    The 1st interval's start and end pair.
     * @param array      $second   The 1st interval's start and end pair.
     * @param array|null $expected The expected interval's start and end pair.
     */
    public function testGap(array $first, array $second, $expected)
    {
        $firstInterval = new Interval(Instant::of($first[0]), Instant::of($first[1]));
        $secondInterval = new Interval(Instant::of($second[0]), Instant::of($second[1]));

        if ($expected !== null) {
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $firstInterval->gap($secondInterval));
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $secondInterval->gap($firstInterval));
        } else {
            $this->assertNull($firstInterval->gap($secondInterval));
            $this->assertNull($secondInterval->gap($firstInterval));
        }
    }

    /**
     * @return array
     */
    public function providerGap()
    {
        return [
            [[3, 7], [0, 1], [1, 3]],
            [[3, 7], [1, 1], [1, 3]],
            [[3, 7], [2, 3], null],  // abuts before
            [[3, 7], [3, 3], null],  // abuts before
            [[3, 7], [4, 6], null],  // overlaps
            [[3, 7], [3, 7], null],  // overlaps
            [[3, 7], [6, 7], null],  // overlaps
            [[3, 7], [7, 7], null],  // abuts before
            [[3, 7], [6, 8], null],  // overlaps
            [[3, 7], [7, 8], null],  // abuts after
            [[3, 7], [8, 8], [7, 8]],
            [[3, 7], [6, 9], null],  // overlaps
            [[3, 7], [7, 9], null],  // abuts after
            [[3, 7], [8, 9], [7, 8]],
            [[3, 7], [9, 9], [7, 9]]
        ];
    }

    /**
     * @dataProvider providerOverlap
     *
     * @param array      $first    The 1st interval's start and end pair.
     * @param array      $second   The 1st interval's start and end pair.
     * @param array|null $expected The expected interval's start and end pair.
     */
    public function testOverlap(array $first, array $second, $expected)
    {
        $firstInterval = new Interval(Instant::of($first[0]), Instant::of($first[1]));
        $secondInterval = new Interval(Instant::of($second[0]), Instant::of($second[1]));

        if ($expected !== null) {
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $firstInterval->overlap($secondInterval));
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $secondInterval->overlap($firstInterval));
        } else {
            $this->assertNull($firstInterval->overlap($secondInterval));
            $this->assertNull($secondInterval->overlap($firstInterval));
        }
    }

    /**
     * @return array
     */
    public function providerOverlap()
    {
        return [
            [[3, 7], [1, 2],   null],  // gap before
            [[3, 7], [2, 2],   null],  // gap before

            [[3, 7], [2, 3],   null],  // abuts before
            [[3, 7], [3, 3],   null],  // abuts before

            [[3, 7], [2, 4], [3, 4]],  // truncated start
            [[3, 7], [3, 4], [3, 4]],
            [[3, 7], [4, 4], [4, 4]],

            [[3, 7], [2, 7], [3, 7]],  // truncated start
            [[3, 7], [3, 7], [3, 7]],
            [[3, 7], [4, 7], [4, 7]],
            [[3, 7], [5, 7], [5, 7]],
            [[3, 7], [6, 7], [6, 7]],
            [[3, 7], [7, 7],   null],  // abuts after

            [[3, 7], [2, 8], [3, 7]],  // truncated start and end
            [[3, 7], [3, 8], [3, 7]],  // truncated end
            [[3, 7], [4, 8], [4, 7]],  // truncated end
            [[3, 7], [5, 8], [5, 7]],  // truncated end
            [[3, 7], [6, 8], [6, 7]],  // truncated end
            [[3, 7], [7, 8],   null],  // abuts after
            [[3, 7], [8, 8],   null],  // gap after
        ];
    }

    /**
     * @dataProvider providerCover
     *
     * @param array      $first    The 1st interval's start and end pair.
     * @param array      $second   The 1st interval's start and end pair.
     * @param array|null $expected The expected interval's start and end pair.
     */
    public function testCover(array $first, array $second, $expected)
    {
        $firstInterval = new Interval(Instant::of($first[0]), Instant::of($first[1]));
        $secondInterval = new Interval(Instant::of($second[0]), Instant::of($second[1]));

        $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $firstInterval->cover($secondInterval));
        $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $secondInterval->cover($firstInterval));
    }

    /**
     * @return array
     */
    public function providerCover()
    {
        return [
            [[3, 7], [1, 2], [1, 7]],  // gap before
            [[3, 7], [2, 2], [2, 7]],  // gap before

            [[3, 7], [2, 3], [2, 7]],  // abuts before
            [[3, 7], [3, 3], [3, 7]],  // abuts before

            [[3, 7], [2, 4], [2, 7]],  // truncated start
            [[3, 7], [3, 4], [3, 7]],
            [[3, 7], [4, 4], [3, 7]],

            [[3, 7], [2, 7], [2, 7]],  // truncated start
            [[3, 7], [3, 7], [3, 7]],
            [[3, 7], [4, 7], [3, 7]],
            [[3, 7], [5, 7], [3, 7]],
            [[3, 7], [6, 7], [3, 7]],
            [[3, 7], [7, 7], [3, 7]],  // abuts after

            [[3, 7], [2, 8], [2, 8]],  // truncated start and end
            [[3, 7], [3, 8], [3, 8]],  // truncated end
            [[3, 7], [4, 8], [3, 8]],  // truncated end
            [[3, 7], [5, 8], [3, 8]],  // truncated end
            [[3, 7], [6, 8], [3, 8]],  // truncated end
            [[3, 7], [7, 8], [3, 8]],  // abuts after
            [[3, 7], [8, 8], [3, 8]]   // gap after
        ];
    }

    /**
     * @dataProvider providerUnion
     *
     * @param array      $first    The 1st interval's start and end pair.
     * @param array      $second   The 1st interval's start and end pair.
     * @param array|null $expected The expected interval's start and end pair.
     */
    public function testUnion(array $first, array $second, $expected)
    {
        $firstInterval = new Interval(Instant::of($first[0]), Instant::of($first[1]));
        $secondInterval = new Interval(Instant::of($second[0]), Instant::of($second[1]));

        if ($expected !== null) {
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $firstInterval->union($secondInterval));
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $secondInterval->union($firstInterval));
        } else {
            $this->assertNull($firstInterval->union($secondInterval));
            $this->assertNull($secondInterval->union($firstInterval));
        }
    }

    /**
     * @return array
     */
    public function providerUnion()
    {
        return [
            [[3, 7], [1, 2],   null],  // gap before
            [[3, 7], [2, 2],   null],  // gap before

            [[3, 7], [2, 3],   null],  // abuts before
            [[3, 7], [3, 3],   null],  // abuts before

            [[3, 7], [2, 4], [2, 7]],  // truncated start
            [[3, 7], [3, 4], [3, 7]],
            [[3, 7], [4, 4], [3, 7]],

            [[3, 7], [2, 7], [2, 7]],  // truncated start
            [[3, 7], [3, 7], [3, 7]],
            [[3, 7], [4, 7], [3, 7]],
            [[3, 7], [5, 7], [3, 7]],
            [[3, 7], [6, 7], [3, 7]],
            [[3, 7], [7, 7],   null],  // abuts after

            [[3, 7], [2, 8], [2, 8]],  // truncated start and end
            [[3, 7], [3, 8], [3, 8]],  // truncated end
            [[3, 7], [4, 8], [3, 8]],  // truncated end
            [[3, 7], [5, 8], [3, 8]],  // truncated end
            [[3, 7], [6, 8], [3, 8]],  // truncated end
            [[3, 7], [7, 8],   null],  // abuts after
            [[3, 7], [8, 8],   null]   // gap after
        ];
    }

    /**
     * @dataProvider providerJoin
     *
     * @param array      $first    The 1st interval's start and end pair.
     * @param array      $second   The 1st interval's start and end pair.
     * @param array|null $expected The expected interval's start and end pair.
     */
    public function testJoin(array $first, array $second, $expected)
    {
        $firstInterval = new Interval(Instant::of($first[0]), Instant::of($first[1]));
        $secondInterval = new Interval(Instant::of($second[0]), Instant::of($second[1]));

        if ($expected !== null) {
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $firstInterval->join($secondInterval));
            $this->assertIntervalIs($expected[0], 0, $expected[1], 0, $secondInterval->join($firstInterval));
        } else {
            $this->assertNull($firstInterval->join($secondInterval));
            $this->assertNull($secondInterval->join($firstInterval));
        }
    }

    /**
     * @return array
     */
    public function providerJoin()
    {
        return [
            [[3, 7], [1, 2],   null],  // gap before
            [[3, 7], [2, 2],   null],  // gap before

            [[3, 7], [2, 3], [2, 7]],  // abuts before
            [[3, 7], [3, 3], [3, 7]],  // abuts before

            [[3, 7], [2, 4],   null],  // truncated start
            [[3, 7], [3, 4],   null],
            [[3, 7], [4, 4],   null],

            [[3, 7], [2, 7],   null],  // truncated start
            [[3, 7], [3, 7],   null],
            [[3, 7], [4, 7],   null],
            [[3, 7], [5, 7],   null],
            [[3, 7], [6, 7],   null],
            [[3, 7], [7, 7], [3, 7]],  // abuts after

            [[3, 7], [2, 8],   null],  // truncated start and end
            [[3, 7], [3, 8],   null],  // truncated end
            [[3, 7], [4, 8],   null],  // truncated end
            [[3, 7], [5, 8],   null],  // truncated end
            [[3, 7], [6, 8],   null],  // truncated end
            [[3, 7], [7, 8], [3, 8]],  // abuts after
            [[3, 7], [8, 8],   null]   // gap after
        ];
    }

    /**
     * @dataProvider providerAbuts
     *
     * @param integer $h1             The 1st interval's start hour.
     * @param integer $m1             The 1st interval's start minute.
     * @param integer $h2             The 1st interval's end hour.
     * @param integer $m2             The 1st interval's end minute.
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
     * @param integer $h2             The 1st interval's end hour.
     * @param integer $m2             The 1st interval's end minute.
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
     * @param integer $h2             The 1st interval's end hour.
     * @param integer $m2             The 1st interval's end minute.
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

    /**
     * @dataProvider providerIsEqualTo
     *
     * @param integer $s1             The 1st interval's start second.
     * @param integer $n1             The 1st interval's start nano second.
     * @param integer $s2             The 1st interval's end second.
     * @param integer $n2             The 1st interval's end nano second.
     * @param integer $s3             The 2st interval's start second.
     * @param integer $n3             The 2st interval's start nano second.
     * @param integer $s4             The 2nd interval's end second.
     * @param integer $n4             The 2nd interval's end nano second.
     * @param integer $expectedResult The expected result.
     */
    public function testIsEqualTo($s1, $n1, $s2, $n2, $s3, $n3, $s4, $n4, $expectedResult)
    {
        $interval1 = new Interval(Instant::of($s1, $n1), Instant::of($s2, $n2));
        $interval2 = new Interval(Instant::of($s3, $n3), Instant::of($s4, $n4));

        $this->assertSame($expectedResult, $interval1->isEqualTo($interval2));
    }

    /**
     * @return array
     */
    public function providerIsEqualTo()
    {
        return [
            [0, 0, 0, 0, 0, 0, 0, 0, true],
            [0, 0, 1, 0, 0, 0, 1, 0, true],
            [0, 0, 1, 0, 0, 0, 1, 1, false],
            [0, 0, 1, 0, 1, 0, 1, 0, false],
            [1, 0, 1, 0, 0, 0, 1, 0, false],
            [1, 1, 1, 1, 1, 1, 1, 1, true],
        ];
    }

    /**
     * @dataProvider providerToString
     *
     * @param integer $s1             The interval's start second.
     * @param integer $n1             The interval's start nano second.
     * @param integer $s2             The interval's end second.
     * @param integer $n2             The interval's end nano second.
     * @param string  $expectedResult The expected result.
     */
    public function testToString($s1, $n1, $s2, $n2, $expectedResult)
    {
        $interval = new Interval(Instant::of($s1, $n1), Instant::of($s2, $n2));

        $this->assertSame($expectedResult, (string) $interval);
    }

    /**
     * @return array
     */
    public function providerToString()
    {
        return [
            [0, 0, 1, 100000000, '1970-01-01T00:00Z/1970-01-01T00:00:01.1Z'],
        ];
    }
}
