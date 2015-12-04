<?php

namespace Brick\DateTime;

use Brick\DateTime\Parser\DateTimeParseException;
use Brick\DateTime\Parser\DateTimeParser;
use Brick\DateTime\Parser\DateTimeParseResult;
use Brick\DateTime\Parser\IsoParsers;

/**
 * A geographical region where the same time-zone rules apply, such as `Europe/London`.
 */
class TimeZoneRegion extends TimeZone
{
    /**
     * @var \DateTimeZone
     */
    private $zone;

    /**
     * Private constructor. Use a factory method to obtain an instance.
     *
     * @param \DateTimeZone $zone
     */
    private function __construct(\DateTimeZone $zone)
    {
        $this->zone = $zone;
    }

    /**
     * @param string $id The region id.
     *
     * @return TimeZoneRegion
     *
     * @throws DateTimeException If the region id is invalid.
     */
    public static function of($id)
    {
        $id = (string) $id;

        if ($id === '' || $id === 'Z' || $id === 'z' || $id[0] === '+' || $id[0] === '-') {
            // DateTimeZone would accept offsets, but TimeZoneRegion targets regions only.
            throw DateTimeException::unknownTimeZoneRegion($id);
        }

        try {
            return new TimeZoneRegion(new \DateTimeZone($id));
        } catch (\Exception $e) {
            throw DateTimeException::unknownTimeZoneRegion($id);
        }
    }

    /**
     * @param DateTimeParseResult $result
     *
     * @return TimeZoneRegion
     *
     * @throws DateTimeException      If the region is not valid.
     * @throws DateTimeParseException If required fields are missing from the result.
     */
    public static function from(DateTimeParseResult $result)
    {
        $region = $result->getField(Field\TimeZoneRegion::NAME);

        return TimeZoneRegion::of($region);
    }

    /**
     * Returns all the available time-zone identifiers.
     *
     * @param bool $includeObsolete Whether to include obsolete time-zone identifiers. Defaults to false.
     *
     * @return string[] An array of time-zone identifiers.
     */
    public static function getAllIdentifiers($includeObsolete = false)
    {
        return \DateTimeZone::listIdentifiers(
            $includeObsolete
            ? \DateTimeZone::ALL_WITH_BC
            : \DateTimeZone::ALL
        );
    }

    /**
     * Returns the time-zone identifiers for the given country.
     *
     * If the country code is not known, an empty array is returned.
     *
     * @param string $countryCode The ISO 3166-1 two-letter country code.
     *
     * @return string[] An array of time-zone identifiers.
     */
    public static function getIdentifiersForCountry($countryCode)
    {
        return \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countryCode);
    }

    /**
     * Parses a region id, such as 'Europe/London'.
     *
     * @param string               $text
     * @param DateTimeParser|null $parser
     *
     * @return TimeZoneRegion
     *
     * @throws DateTimeParseException
     */
    public static function parse($text, DateTimeParser $parser = null)
    {
        if (! $parser) {
            $parser = IsoParsers::timeZoneRegion();
        }

        return TimeZoneRegion::from($parser->parse($text));
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->zone->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset(ReadableInstant $instant)
    {
        $dateTime = new \DateTime('@' . $instant->getEpochSecond(), new \DateTimeZone('UTC'));

        return $this->zone->getOffset($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function toDateTimeZone()
    {
        return clone $this->zone;
    }
}
