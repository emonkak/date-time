<?php

namespace Brick\DateTime;

/**
 * Represents a period of time between two instants, inclusive of the start instant and exclusive of the end.
 * The end instant is always greater than or equal to the start instant.
 *
 * This class is immutable.
 */
class Interval
{
    /**
     * The start instant, inclusive.
     *
     * @var \Brick\DateTime\ReadableInstant
     */
    private $start;

    /**
     * The end instant, exclusive.
     *
     * @var \Brick\DateTime\ReadableInstant
     */
    private $end;

    /**
     * Class constructor.
     *
     * @param \Brick\DateTime\ReadableInstant $startInclusive The start instant, inclusive.
     * @param \Brick\DateTime\ReadableInstant $endExclusive   The end instant, exclusive.
     *
     * @throws \Brick\DateTime\DateTimeException
     */
    public function __construct(ReadableInstant $startInclusive, ReadableInstant $endExclusive)
    {
        if ($endExclusive->isBefore($startInclusive)) {
            throw new DateTimeException('The end instant must not be before the start instant.');
        }

        $this->start = $startInclusive;
        $this->end = $endExclusive;
    }

    /**
     * Returns the start instant, inclusive, of this Interval.
     *
     * @return \Brick\DateTime\ReadableInstant
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Returns the end instant, exclusive, of this Interval.
     *
     * @return \Brick\DateTime\ReadableInstant
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Returns a copy of this Interval with the start instant altered.
     *
     * @param \Brick\DateTime\ReadableInstant $start
     *
     * @return \Brick\DateTime\Interval
     */
    public function withStart(ReadableInstant $start)
    {
        return new Interval($start, $this->end);
    }

    /**
     * Returns a copy of this Interval with the end instant altered.
     *
     * @param \Brick\DateTime\ReadableInstant $end
     *
     * @return \Brick\DateTime\Interval
     */
    public function withEnd(ReadableInstant $end)
    {
        return new Interval($this->start, $end);
    }

    /**
     * Gets the gap between this interval and another interval.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return \Brick\DateTime\Interval
     */
    public function gap(Interval $interval)
    {
        $otherStart = $interval->start;
        $otherEnd = $interval->end;
        $thisStart = $this->start;
        $thisEnd = $this->end;
        if ($thisStart->isAfter($otherEnd)) {
            return new Interval($otherEnd, $thisStart);
        } else if ($otherStart->isAfter($thisEnd)) {
            return new Interval($thisEnd, $otherStart);
        } else {
            return null;
        }
    }

    /**
     * Gets the overlap between this interval and another interval.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return \Brick\DateTime\Interval
     */
    public function overlap(Interval $interval)
    {
        if (!$this->overlaps($interval)) {
            return null;
        }
        $otherStart = $interval->start;
        $otherEnd = $interval->end;
        $thisStart = $this->start;
        $thisEnd = $this->end;
        $start = $thisStart->isAfter($otherStart) ? $thisStart : $otherStart;
        $end = $thisEnd->isBefore($otherEnd) ? $thisEnd : $otherEnd;
        return new Interval($start, $end);
    }

    /**
     * Gets the covered interval between this interval and another interval.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return \Brick\DateTime\Interval
     */
    public function cover(Interval $interval)
    {
        $otherStart = $interval->start;
        $otherEnd = $interval->end;
        $thisStart = $this->start;
        $thisEnd = $this->end;
        $start = $thisStart->isBefore($otherStart) ? $thisStart : $otherStart;
        $end = $thisEnd->isAfter($otherEnd) ? $thisEnd : $otherEnd;
        return new Interval($start, $end);
    }

    /**
     * Gets the union between this interval and another interval.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return \Brick\DateTime\Interval
     */
    public function union(Interval $interval)
    {
        if (!$this->overlaps($interval)) {
            return null;
        }
        return $this->cover($interval);
    }

    /**
     * Joins the interval between the adjacent.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return \Brick\DateTime\Interval
     */
    public function join(Interval $interval)
    {
        if (!$this->abuts($interval)) {
            return null;
        }
        return $this->cover($interval);
    }

    /**
     * Returns a Duration representing the time elapsed in this Interval.
     *
     * @return \Brick\DateTime\Duration
     */
    public function getDuration()
    {
        return Duration::between($this->start, $this->end);
    }

    /**
     * Does this interval abut with the interval specified.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return boolean
     */
    public function abuts(Interval $interval)
    {
        $otherStart = $interval->start;
        $otherEnd = $interval->end;
        $thisStart = $this->start;
        $thisEnd = $this->end;
        return $otherEnd->isEqualTo($thisStart) || $thisEnd->isEqualTo($otherStart);
    }

    /**
     * Does this time interval contain the specified time interval.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return boolean
     */
    public function contains(Interval $interval)
    {
        $otherStart = $interval->start;
        $otherEnd = $interval->end;
        $thisStart = $this->start;
        $thisEnd = $this->end;
        return $thisStart->compareTo($otherStart) <= 0 && $otherStart->compareTo($thisEnd) < 0 && $otherEnd->compareTo($thisEnd) <= 0;
    }

    /**
     * Does this time interval contain the specified instant.
     *
     * @param \Brick\DateTime\ReadableInstant $instant
     *
     * @return boolean
     */
    public function containsInstant(ReadableInstant $instant)
    {
        $thisStart = $this->start;
        $thisEnd = $this->end;
        return $instant->compareTo($thisStart) >= 0 && $instant->compareTo($thisEnd) < 0;
    }

    /**
     * Checks if this Interval is equal to the specified time.
     *
     * @param Interval $that The interval to compare to.
     *
     * @return boolean
     */
    public function isEqualTo(Interval $that)
    {
        return $this->start->isEqualTo($that->start) && $this->end->isEqualTo($that->end);
    }

    /**
     * Does this time interval overlap the specified time interval.
     *
     * @param \Brick\DateTime\Interval $interval
     *
     * @return boolean
     */
    public function overlaps(Interval $interval)
    {
        $otherStart = $interval->start;
        $otherEnd = $interval->end;
        $thisStart = $this->start;
        $thisEnd = $this->end;
        return $thisStart->compareTo($otherEnd) < 0 && $otherStart->compareTo($thisEnd) < 0;
    }

    /**
     * Returns a string in ISO8601 interval format.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->start . '/' . $this->end;
    }
}
