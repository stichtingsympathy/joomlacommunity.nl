<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Date Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Date
 */
interface KDateInterface extends KObjectHandlable
{
    /**
     * Returns the date formatted according to given format.
     *
     * @param  string $format The format to use
     * @return string
     */
    public function format($format);

    /**
     * Returns human readable date.
     *
     * @param  string $period The smallest period to use. Default is 'second'.
     * @return string Formatted date.
     */
    public function humanize($period = 'second');

    /**
     * Alters the timestamp
     *
     * @param string $modify A date/time string
     * @return KDateInterface or FALSE on failure.
     */
    public function modify($modify);

    /**
     * Resets the current date of the DateTime object to a different date.
     *
     * @param integer $year     Year of the date.
     * @param integer $month    Month of the date.
     * @param integer $day      Day of the date.
     * @return KDateInterface or FALSE on failure.
     */
    public function setDate($year , $month , $day);

    /**
     * Resets the current date of the DateTime object to a different date.
     *
     * @param integer $hour     Hour of the time.
     * @param integer $minute   Minute of the time.
     * @param integer $second  Second of the time.
     * @return KDateInterface or FALSE on failure.
     */
    public function setTime($hour, $minute, $second = 0);

    /**
     * Return time zone relative to given DateTime
     *
     * @return DateTimeZone Return a DateTimeZone object on success or FALSE on failure.
     */
    public function getTimezone();

    /**
     * Sets the date and time based on an Unix timestamp.
     *
     * @param DateTimeZone $timezone A DateTimeZone object representing the desired time zone.
     * @return KDateInterface or FALSE on failure.
     */
    public function setTimezone(DateTimeZone $timezone);

    /**
     * Gets the Unix timestamp.
     *
     * @return integer
     */
    public function getTimestamp();

    /**
     * Returns the timezone offset.
     *
     * @return integer
     */
    public function getOffset();
}