<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */

namespace Armenio\I18nFilterDateTime\Filter;

use IntlDateFormatter;
use IntlException;
use Locale;
use Traversable;
use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception as FilterException;
use Zend\I18n\Exception as I18nException;

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
     * Sets filter options
     *
     * @param array|\Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
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
     * Returns a non lenient configured IntlDateFormatter
     *
     * @return IntlDateFormatter
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $dateType = $this->getDateType();
        $timeType = $this->getTimeType();
        $locale = $this->getLocale();
        $timezone = $this->getTimezone();
        $calendar = $this->getCalendar();

        try {
            $formatter = new IntlDateFormatter(
                $locale,
                $dateType,
                $timeType,
                $timezone,
                $calendar
            );

            $formatter->setLenient(false);
        } catch (IntlException $intlException) {
            // throw new FilterException\InvalidArgumentException($intlException->getMessage(), 0, $intlException);
            return $value;
        }

        $this->setTimezone($formatter->getTimezone()->getID());
        $this->setCalendar($formatter->getCalendar());

        $timestamp = $formatter->parse($value);

        $formatter->setPattern($this->getPattern());

        $formatted = $formatter->format($timestamp);

        if (intl_is_failure($formatter->getErrorCode())) {
            // throw new FilterException\InvalidArgumentException($formatter->getErrorMessage());
            return $value;
        }

        return $formatted;
    }
}