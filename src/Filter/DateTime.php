<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18nFilterDateTime\Filter;

use IntlDateFormatter;
use Locale;
use Zend\Filter\AbstractFilter;

/**
 * Class DateTime
 * @package Armenio\I18nFilterDateTime\Filter
 */
class DateTime extends AbstractFilter
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var int
     */
    protected $dateType;

    /**
     * @var int
     */
    protected $timeType;

    /**
     * Optional timezone
     *
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var int
     */
    protected $calendar;

    /**
     * DateTime constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Sets the calendar to be used by the IntlDateFormatter
     *
     * @param int|null $calendar
     * @return DateTime provides fluent interface
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Returns the calendar to by the IntlDateFormatter
     *
     * @return int
     */
    public function getCalendar()
    {
        return $this->calendar ?: IntlDateFormatter::GREGORIAN;
    }

    /**
     * Sets the date format to be used by the IntlDateFormatter
     *
     * @param int|null $dateType
     * @return DateTime provides fluent interface
     */
    public function setDateType($dateType)
    {
        $this->dateType = $dateType;

        return $this;
    }

    /**
     * Returns the date format used by the IntlDateFormatter
     *
     * @return int
     */
    public function getDateType()
    {
        return $this->dateType;
    }

    /**
     * Sets the pattern to be used by the IntlDateFormatter
     *
     * @param string|null $pattern
     * @return DateTime provides fluent interface
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Returns the pattern used by the IntlDateFormatter
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Sets the time format to be used by the IntlDateFormatter
     *
     * @param int|null $timeType
     * @return DateTime provides fluent interface
     */
    public function setTimeType($timeType)
    {
        $this->timeType = $timeType;

        return $this;
    }

    /**
     * Returns the time format used by the IntlDateFormatter
     *
     * @return int
     */
    public function getTimeType()
    {
        return $this->timeType;
    }

    /**
     * Set locale to use instead of the default
     *
     * @param  string $locale
     * @return DateTime provides fluent interface
     */
    public function setLocale($locale)
    {
        $this->locale = (string)$locale;

        return $this;
    }

    /**
     * Get the locale to use
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->locale ?: Locale::getDefault();
    }

    /**
     * Set timezone to use instead of the default
     *
     * @param  string $timezone
     * @return DateTime provides fluent interface
     */
    public function setTimezone($timezone)
    {
        $this->timezone = (string)$timezone;

        return $this;
    }

    /**
     * Get the timezone to use
     *
     * @return string|null
     */
    public function getTimezone()
    {
        return $this->timezone ?: date_default_timezone_get();
    }

    /**
     * Returns a date string formatted by IntlDateFormatter
     *
     * @param mixed $value
     * @return mixed|string
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $locale = $this->getLocale();
        $dateType = $this->getDateType();
        $timeType = $this->getTimeType();
        $timezone = $this->getTimezone();
        $calendar = $this->getCalendar();

        $formatter = new IntlDateFormatter(
            $locale,
            $dateType,
            $timeType,
            $timezone,
            $calendar
        );

        if (false === $formatter || intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        $formatter->setLenient(false);

        // parse current date to timestamp
        $timestamp = $formatter->parse($value);

        // get new pattern
        $pattern = $this->getPattern();

        $formatter->setPattern($pattern);

        $formatted = $formatter->format($timestamp);

        if (intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        return $formatted;
    }
}