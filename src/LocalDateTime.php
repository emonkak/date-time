<?php

namespace Brick\DateTime;

use Brick\DateTime\Parser\DateTimeParseException;
use Brick\DateTime\Parser\DateTimeParser;
use Brick\DateTime\Parser\DateTimeParseResult;
use Brick\DateTime\Parser\IsoParsers;
use Brick\DateTime\Utility\Math;
use Brick\DateTime\Utility\Cast;

/**
 * A date-time without a time-zone in the ISO-8601 calendar system, such as 2007-12-03T10:15:30.
 *
 * This class is immutable.
 */
class LocalDateTime implements DateTimeAccessor
{
    /**
     * @var LocalDate
     */
    private $date;

    /**
     * @var LocalTime
     */
    private $time;

    /**
     * Class constructor.
     *
     * @param LocalDate $date
     * @param LocalTime $time
     */
    public function __construct(LocalDate $date, LocalTime $time)
    {
        $this->date = $date;
        $this->time = $time;
    }

    /**
     * @param integer $year   The year, from MIN_YEAR to MAX_YEAR.
     * @param integer $month  The month-of-year, from 1 (January) to 12 (December).
     * @param integer $day    The day-of-month, from 1 to 31.
     * @param integer $hour   The hour-of-day, from 0 to 23.
     * @param integer $minute The minute-of-hour, from 0 to 59.
     * @param integer $second The second-of-minute, from 0 to 59.
     * @param integer $nano   The nano-of-second, from 0 to 999,999,999.
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException If the date or time is not valid.
     */
    public static function of($year, $month, $day, $hour = 0, $minute = 0, $second = 0, $nano = 0)
    {
        $date = LocalDate::of($year, $month, $day);
        $time = LocalTime::of($hour, $minute, $second, $nano);

        return new LocalDateTime($date, $time);
    }

    /**
     * Creates a LocalDateTime from an instant.
     *
     * @param ReadableInstant $instant
     * @param TimeZone        $timeZone
     *
     * @return LocalDateTime
     */
    public static function ofInstant(ReadableInstant $instant, TimeZone $timeZone)
    {
        $localSecond = $instant->getEpochSecond() + $timeZone->getOffset($instant);
        $localEpochDay = Math::floorDiv($localSecond, LocalTime::SECONDS_PER_DAY);
        $secondOfDay = Math::floorMod($localSecond, LocalTime::SECONDS_PER_DAY);
        $nano = $instant->getNano();
        $date = LocalDate::ofEpochDay($localEpochDay);
        $time = LocalTime::ofSecondOfDay($secondOfDay, $nano);
        return new LocalDateTime($date, $time);
    }

    /**
     * @param TimeZone $timeZone
     *
     * @return LocalDateTime
     */
    public static function now(TimeZone $timeZone)
    {
        return ZonedDateTime::now($timeZone)->getDateTime();
    }

    /**
     * @param DateTimeParseResult $result
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException      If the date-time is not valid.
     * @throws DateTimeParseException If required fields are missing from the result.
     */
    public static function from(DateTimeParseResult $result)
    {
        return new LocalDateTime(
            LocalDate::from($result),
            LocalTime::from($result)
        );
    }

    /**
     * Obtains an instance of `LocalDateTime` from a text string.
     *
     * @param string              $text   The text to parse, such as `2007-12-03T10:15:30`.
     * @param DateTimeParser|null $parser The parser to use, defaults to the ISO 8601 parser.
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException      If the date-time is not valid.
     * @throws DateTimeParseException If the text string does not follow the expected format.
     */
    public static function parse($text, DateTimeParser $parser = null)
    {
        if (! $parser) {
            $parser = IsoParsers::localDateTime();
        }

        return LocalDateTime::from($parser->parse($text));
    }

    /**
     * Returns the smallest possible value for LocalDateTime.
     *
     * @return LocalDateTime
     */
    public static function min()
    {
        return new LocalDateTime(LocalDate::min(), LocalTime::min());
    }

    /**
     * Returns the highest possible value for LocalDateTime.
     *
     * @return LocalDateTime
     */
    public static function max()
    {
        return new LocalDateTime(LocalDate::max(), LocalTime::max());
    }

    /**
     * Returns the smallest LocalDateTime among the given values.
     *
     * @param LocalDateTime[] $times The LocalDateTime objects to compare.
     *
     * @return LocalDateTime The earliest LocalDateTime object.
     *
     * @throws DateTimeException If the array is empty.
     */
    public static function minOf(array $times)
    {
        if (! $times) {
            throw new DateTimeException(__METHOD__ . ' does not accept less than 1 parameter.');
        }

        $min = LocalDateTime::max();

        foreach ($times as $time) {
            if ($time->isBefore($min)) {
                $min = $time;
            }
        }

        return $min;
    }

    /**
     * Returns the highest LocalDateTime among the given values.
     *
     * @param LocalDateTime[] $times The LocalDateTime objects to compare.
     *
     * @return LocalDateTime The latest LocalDateTime object.
     *
     * @throws DateTimeException If the array is empty.
     */
    public static function maxOf(array $times)
    {
        if (! $times) {
            throw new DateTimeException(__METHOD__ . ' does not accept less than 1 parameter.');
        }

        $max = LocalDateTime::min();

        foreach ($times as $time) {
            if ($time->isAfter($max)) {
                $max = $time;
            }
        }

        return $max;
    }

    /**
     * @return LocalDate
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return LocalTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return integer
     */
    public function getYear()
    {
        return $this->date->getYear();
    }

    /**
     * @return integer
     */
    public function getMonth()
    {
        return $this->date->getMonth();
    }

    /**
     * @return integer
     */
    public function getDay()
    {
        return $this->date->getDay();
    }

    /**
     * @return DayOfWeek
     */
    public function getDayOfWeek()
    {
        return $this->date->getDayOfWeek();
    }

    /**
     * @return integer
     */
    public function getDayOfYear()
    {
        return $this->date->getDayOfYear();
    }

    /**
     * @return integer
     */
    public function getHour()
    {
        return $this->time->getHour();
    }

    /**
     * @return integer
     */
    public function getMinute()
    {
        return $this->time->getMinute();
    }

    /**
     * @return integer
     */
    public function getSecond()
    {
        return $this->time->getSecond();
    }

    /**
     * @return integer
     */
    public function getNano()
    {
        return $this->time->getNano();
    }

    /**
     * Converts this date-time to number of seconds since the epoch of 1970-01-01T00:00:00Z.
     *
     * @param TimeZone $timeZone
     *
     * @return integer
     */
    public function toEpochSecond(TimeZone $timeZone)
    {
        $epochDay = $this->date->toEpochDay();
        $seconds = $epochDay * LocalTime::SECONDS_PER_DAY + $this->time->toSecondOfDay();
        $seconds -= $timeZone->getOffset(Instant::of($seconds, $this->time->getNano()));
        return $seconds;
    }

    /**
     * Converts this date-time to a point in time.
     *
     * @param TimeZone $timeZone
     *
     * @return Instant
     */
    public function toInstant(TimeZone $timeZone)
    {
        return Instant::of($this->toEpochSecond($timeZone), $this->time->getNano());
    }

    /**
     * Returns a copy of this date-time with the new date and time, checking
     * to see if a new object is in fact required.
     *
     * @param LocalDate $date
     * @param LocalTime $time
     *
     * @return LocalDateTime
     */
    private function with(LocalDate $date, LocalTime $time)
    {
        if ($date->isEqualTo($this->date) && $time->isEqualTo($this->time)) {
            return $this;
        }

        return new LocalDateTime($date, $time);
    }

    /**
     * Returns a copy of this LocalDateTime with the date altered.
     *
     * @param LocalDate $date
     *
     * @return LocalDateTime
     */
    public function withDate(LocalDate $date)
    {
        if ($date->isEqualTo($this->date)) {
            return $this;
        }

        return new LocalDateTime($date, $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the time altered.
     *
     * @param LocalTime $time
     *
     * @return LocalDateTime
     */
    public function withTime(LocalTime $time)
    {
        if ($time->isEqualTo($this->time)) {
            return $this;
        }

        return new LocalDateTime($this->date, $time);
    }

    /**
     * Returns a copy of this LocalDateTime with the year altered.
     *
     * If the day-of-month is invalid for the year, it will be changed to the last valid day of the month.
     *
     * @param integer $year
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException If the year is outside the valid range.
     */
    public function withYear($year)
    {
        return $this->with($this->date->withYear($year), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the month-of-year altered.
     *
     * If the day-of-month is invalid for the month and year, it will be changed to the last valid day of the month.
     *
     * @param integer $month
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException If the month is invalid.
     */
    public function withMonth($month)
    {
        return $this->with($this->date->withMonth($month), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the day-of-month altered.
     *
     * If the resulting date is invalid, an exception is thrown.
     *
     * @param integer $day
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException If the day is invalid for the current year and month.
     */
    public function withDay($day)
    {
        return $this->with($this->date->withDay($day), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the hour-of-day altered.
     *
     * @param integer $hour
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException If the hour is invalid.
     */
    public function withHour($hour)
    {
        return $this->with($this->date, $this->time->withHour($hour));
    }

    /**
     * Returns a copy of this LocalDateTime with the minute-of-hour altered.
     *
     * @param integer $minute
     *
     * @return LocalDateTime
     */
    public function withMinute($minute)
    {
        return $this->with($this->date, $this->time->withMinute($minute));
    }

    /**
     * Returns a copy of this LocalDateTime with the second-of-minute altered.
     *
     * @param integer $second
     *
     * @return LocalDateTime
     */
    public function withSecond($second)
    {
        return $this->with($this->date, $this->time->withSecond($second));
    }

    /**
     * Returns a copy of this LocalDateTime with the nano-of-second altered.
     *
     * @param integer $nano
     *
     * @return LocalDateTime
     */
    public function withNano($nano)
    {
        return $this->with($this->date, $this->time->withNano($nano));
    }

    /**
     * Returns a zoned date-time formed from this date-time and the specified time-zone.
     *
     * @param TimeZone $zone The zime-zone to use.
     *
     * @return ZonedDateTime The zoned date-time formed from this date-time.
     */
    public function atTimeZone(TimeZone $zone)
    {
        return ZonedDateTime::of($this, $zone);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified Period added.
     *
     * @param Period $period
     *
     * @return LocalDateTime
     */
    public function plusPeriod(Period $period)
    {
        return $this->with($this->date->plusPeriod($period), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specific Duration added.
     *
     * @param Duration $duration
     *
     * @return LocalDateTime
     */
    public function plusDuration(Duration $duration)
    {
        $days = Math::floorDiv($duration->getSeconds(), LocalTime::SECONDS_PER_DAY);

        return $this->with($this->date->plusDays($days), $this->time->plusDuration($duration));
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in years added.
     *
     * @param integer $years
     *
     * @return LocalDateTime
     */
    public function plusYears($years)
    {
        return $this->with($this->date->plusYears($years), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in months added.
     *
     * @param integer $months
     *
     * @return LocalDateTime
     */
    public function plusMonths($months)
    {
        return $this->with($this->date->plusMonths($months), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in weeks added.
     *
     * @param integer $weeks
     *
     * @return LocalDateTime
     */
    public function plusWeeks($weeks)
    {
        return $this->with($this->date->plusWeeks($weeks), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in days added.
     *
     * @param integer $days
     *
     * @return LocalDateTime
     */
    public function plusDays($days)
    {
        return $this->with($this->date->plusDays($days), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in hours added.
     *
     * @param integer $hours
     *
     * @return LocalDateTime
     */
    public function plusHours($hours)
    {
        $hours = Cast::toInteger($hours);

        if ($hours === 0) {
            return $this;
        }

        return $this->plusWithOverflow($hours, 0, 0, 0, 1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in minutes added.
     *
     * @param integer $minutes
     *
     * @return LocalDateTime
     */
    public function plusMinutes($minutes)
    {
        $minutes = Cast::toInteger($minutes);

        if ($minutes === 0) {
            return $this;
        }

        return $this->plusWithOverflow(0, $minutes, 0, 0, 1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in seconds added.
     *
     * @param integer $seconds
     *
     * @return LocalDateTime
     */
    public function plusSeconds($seconds)
    {
        $seconds = Cast::toInteger($seconds);

        if ($seconds === 0) {
            return $this;
        }

        return $this->plusWithOverflow(0, 0, $seconds, 0, 1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in nanoseconds added.
     *
     * @param integer $nanos
     *
     * @return LocalDateTime
     */
    public function plusNanos($nanos)
    {
        $nanos = Cast::toInteger($nanos);

        if ($nanos === 0) {
            return $this;
        }

        return $this->plusWithOverflow(0, 0, 0, $nanos, 1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified Period subtracted.
     *
     * @param Period $period
     *
     * @return LocalDateTime
     */
    public function minusPeriod(Period $period)
    {
        return $this->with($this->date->minusPeriod($period), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specific Duration subtracted.
     *
     * @param Duration $duration
     *
     * @return LocalDateTime
     */
    public function minusDuration(Duration $duration)
    {
        return $this->plusDuration($duration->negated());
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in years subtracted.
     *
     * @param integer $years
     *
     * @return LocalDateTime
     */
    public function minusYears($years)
    {
        return $this->with($this->date->minusYears($years), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in months subtracted.
     *
     * @param integer $months
     *
     * @return LocalDateTime
     */
    public function minusMonths($months)
    {
        return $this->with($this->date->minusMonths($months), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in weeks subtracted.
     *
     * @param integer $weeks
     *
     * @return LocalDateTime
     */
    public function minusWeeks($weeks)
    {
        return $this->with($this->date->minusWeeks($weeks), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in days subtracted.
     *
     * @param integer $days
     *
     * @return LocalDateTime
     */
    public function minusDays($days)
    {
        return $this->with($this->date->minusDays($days), $this->time);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in hours subtracted.
     *
     * @param integer $hours
     *
     * @return LocalDateTime
     */
    public function minusHours($hours)
    {
        $hours = Cast::toInteger($hours);

        if ($hours === 0) {
            return $this;
        }

        return $this->plusWithOverflow($hours, 0, 0, 0, -1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in minutes subtracted.
     *
     * @param integer $minutes
     *
     * @return LocalDateTime
     */
    public function minusMinutes($minutes)
    {
        $minutes = Cast::toInteger($minutes);

        if ($minutes === 0) {
            return $this;
        }

        return $this->plusWithOverflow(0, $minutes, 0, 0, -1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in seconds subtracted.
     *
     * @param integer $seconds
     *
     * @return LocalDateTime
     */
    public function minusSeconds($seconds)
    {
        $seconds = Cast::toInteger($seconds);

        if ($seconds === 0) {
            return $this;
        }

        return $this->plusWithOverflow(0, 0, $seconds, 0, -1);
    }

    /**
     * Returns a copy of this LocalDateTime with the specified period in nanoseconds subtracted.
     *
     * @param integer $nanos
     *
     * @return LocalDateTime
     */
    public function minusNanos($nanos)
    {
        $nanos = Cast::toInteger($nanos);

        if ($nanos === 0) {
            return $this;
        }

        return $this->plusWithOverflow(0, 0, 0, $nanos, -1);
    }

    /**
     * Returns a copy of this `LocalDateTime` with the specified period added.
     *
     * @param integer $hours   The hours to add, validated as an integer. May be negative.
     * @param integer $minutes The minutes to add, validated as an integer. May be negative.
     * @param integer $seconds The seconds to add, validated as an integer. May be negative.
     * @param integer $nanos   The nanos to add, validated as an integer. May be negative.
     * @param integer $sign    The sign, validated as an integer of value `1` to add or `-1` to subtract.
     *
     * @return LocalDateTime The combined result.
     */
    private function plusWithOverflow($hours, $minutes, $seconds, $nanos, $sign)
    {
        $totDays =
            Math::div($hours, LocalTime::HOURS_PER_DAY) +
            Math::div($minutes, LocalTime::MINUTES_PER_DAY) +
            Math::div($seconds, LocalTime::SECONDS_PER_DAY);
        $totDays *= $sign;

        $totSeconds =
            ($seconds % LocalTime::SECONDS_PER_DAY) +
            ($minutes % LocalTime::MINUTES_PER_DAY) * LocalTime::SECONDS_PER_MINUTE +
            ($hours % LocalTime::HOURS_PER_DAY) * LocalTime::SECONDS_PER_HOUR;

        $curSoD = $this->time->toSecondOfDay();
        $totSeconds = $totSeconds * $sign + $curSoD;

        $totNanos = $nanos * $sign + $this->time->getNano();
        $totSeconds += Math::floorDiv($totNanos, LocalTime::NANOS_PER_SECOND);
        $newNano = Math::floorMod($totNanos, LocalTime::NANOS_PER_SECOND);

        $totDays += Math::floorDiv($totSeconds, LocalTime::SECONDS_PER_DAY);
        $newSoD = Math::floorMod($totSeconds, LocalTime::SECONDS_PER_DAY);

        $newTime = ($newSoD === $curSoD ? $this->time : LocalTime::ofSecondOfDay($newSoD, $newNano));

        return $this->with($this->date->plusDays($totDays), $newTime);
    }

    /**
     * Returns a copy of this with the time truncated.
     *
     * @param Duration $unit
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException if the unit is not supported
     */
    public function truncatedTo(Duration $unit)
    {
        return $this->withTime($this->time->truncatedTo($unit));
    }

    /**
     * Returns a copy of this with the time rounded.
     *
     * @param Duration $unit
     *
     * @return LocalDateTime
     *
     * @throws DateTimeException if the unit is not supported
     */
    public function roundedTo(Duration $unit)
    {
        return $this->withTime($this->time->roundedTo($unit));
    }

    /**
     * Compares this date-time to another date-time.
     *
     * @param LocalDateTime $that The date-time to compare to.
     *
     * @return integer [-1,0,1] If this date-time is before, on, or after the given date-time.
     */
    public function compareTo(LocalDateTime $that)
    {
        return $this->date->compareTo($that->date) ?: $this->time->compareTo($that->time);
    }

    /**
     * @param LocalDateTime $that
     *
     * @return boolean
     */
    public function isEqualTo(LocalDateTime $that)
    {
        return $this->compareTo($that) === 0;
    }

    /**
     * @param LocalDateTime $that
     *
     * @return boolean
     */
    public function isBefore(LocalDateTime $that)
    {
        return $this->compareTo($that) === -1;
    }

    /**
     * @param LocalDateTime $that
     *
     * @return boolean
     */
    public function isBeforeOrEqualTo(LocalDateTime $that)
    {
        return $this->compareTo($that) <= 0;
    }

    /**
     * @param LocalDateTime $that
     *
     * @return boolean
     */
    public function isAfter(LocalDateTime $that)
    {
        return $this->compareTo($that) === 1;
    }

    /**
     * @param LocalDateTime $that
     *
     * @return boolean
     */
    public function isAfterOrEqualTo(LocalDateTime $that)
    {
        return $this->compareTo($that) >= 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($field)
    {
        $value = $this->date->getField($field);

        if ($value !== null) {
            return $value;
        }

        return $this->time->getField($field);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->date . 'T' . $this->time;
    }
}
