<?php

namespace Zoolanders\Framework\Model\Database;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Model\Database;

trait Date
{
    /**
     * @return \JDatabaseQuery
     */
    abstract public function getQuery ();

    /**
     * @param $fieldOrCallable
     * @param $operator
     * @param $value
     * @return $this
     */
    abstract public function where ($fieldOrCallable, $operator, $value);

    /**
     * @param $sql
     * @return $this
     */
    abstract public function whereRaw ($sql);

    /**
     * @param $sql
     * @return $this
     */
    abstract public function orWhereRaw ($sql);

    /**
     * @param $fieldOrCallable
     * @param $operator
     * @param $value
     * @return $this
     */
    abstract public function orWhere ($fieldOrCallable, $operator, $value);

    /**
     * @var array
     */
    protected static $validIntervals = array('MONTH', 'DAY', 'WEEK', 'YEAR', 'MINUTE', 'SECOND', 'HOUR');

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function whereDateBetween ($field, $valueFrom, $valueTo)
    {
        $from = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDayStart($valueFrom));
        $to = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDayEnd($valueTo));

        $field = $this->getQuery()->qn($field);

        $this->whereRaw("({$field} BETWEEN {$from} AND {$to})");

        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function orWhereDateBetween ($field, $valueFrom, $valueTo)
    {
        $from = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDayStart($valueFrom));
        $to = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDayEnd($valueTo));

        $field = $this->getQuery()->qn($field);

        $this->orWhereRaw("({$field} BETWEEN {$from} AND {$to})");

        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function whereDateTimeBetween ($field, $valueFrom, $valueTo)
    {
        $from = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDateTime($valueFrom));
        $to = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDateTime($valueTo));

        $field = $this->getQuery()->qn($field);

        $this->whereRaw("({$field} BETWEEN {$from} AND {$to})");

        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function orWhereDateTimeBetween ($field, $valueFrom, $valueTo)
    {
        $from = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDateTime($valueFrom));
        $to = $this->getQuery()->q(\Zoolanders\Framework\Service\Date::getDateTime($valueTo));

        $field = $this->getQuery()->qn($field);

        $this->orWhereRaw("({$field} BETWEEN {$from} AND {$to})");

        return $this;
    }

    /**
     * Filter based on an interval
     * Something like "within 2 weeks of yesterday"
     *
     * @param $field string
     * @param $interval int
     * @param $unit string
     * @return $this
     */
    public function whereDateWithinInterval ($field, $interval, $unit)
    {
        $unit = strtoupper(trim($unit));
        $interval = (int)$interval;

        if (!$this->isValidInterval($unit)) {
            $unit = 'WEEK';
        }

        $field = $this->getQuery()->qn($field);

        if ($interval > 0) {
            $this->whereRaw("( {$field} BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL {$interval} {$unit}))");
            return $this;
        }

        $this->whereRaw(" {$field} BETWEEN DATE_ADD(NOW(), INTERVAL {$interval} {$unit}) AND NOW())");
        return $this;
    }

    /**
     * Filter based on an interval
     * Something like "within 2 weeks of yesterday"
     *
     * @param $field string
     * @param $interval int
     * @param $unit string
     * @return $this
     */
    public function orWhereDateWithinInterval ($field, $interval, $unit)
    {
        $unit = strtoupper(trim($unit));
        $interval = (int)$interval;

        if (!$this->isValidInterval($unit)) {
            $unit = 'WEEK';
        }

        $field = $this->getQuery()->qn($field);

        if ($interval > 0) {
            $this->orWhereRaw("( {$field} BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL {$interval} {$unit}))");
            return $this;
        }

        $this->orWhereRaw(" {$field} BETWEEN DATE_ADD(NOW(), INTERVAL {$interval} {$unit}) AND NOW())");
        return $this;
    }

    /**
     * @param $unit
     * @return bool
     */
    public function isValidInterval ($unit)
    {
        return in_array($unit, self::$validIntervals);
    }

    /**
     * @param $field
     * @param $date
     * @return $this
     */
    public function whereDate ($field, $operator, $date)
    {
        return $this->doWhereDate($field, $operator, $date, "AND");
    }

    /**
     * @param $field
     * @param $date
     * @return $this
     */
    public function orWhereDate ($field, $operator, $date)
    {
        return $this->doWhereDate($field, $operator, $date, "OR");
    }

    /**
     * @param $field
     * @param $date
     * @return $this
     */
    protected function doWhereDate ($field, $operator, $date, $mode)
    {
        $from = \Zoolanders\Framework\Service\Date::getDayStart($date);
        $to = \Zoolanders\Framework\Service\Date::getDayEnd($date);

        switch ($operator) {
            // Special case: equal a date means within a day => betwen start day and end day
            case '=':
            case 'LIKE':
                $this->whereRaw("('{$this->getQuery()->qn($field)}' BETWEEN {$this->getQuery()->q($from)} AND {$this->getQuery()->q($to)})");
                return $this;
                break;

            // From => from start of the day
            case '>';
            case '>=':
                $value = $from;
                break;

            // To => from start of the day
            case '<';
            case '<=':
                $value = $to;
                break;

            // Fallback to the start of the day
            default:
                $value = $from;
                break;
        }

        if ($mode == "AND") {
            // proxy to the where call
            $this->where($field, $operator, $value);
        } else {
            // proxy to the where call
            $this->orWhere($field, $operator, $value);
        }

        return $this;
    }

    /**
     * Filter based on EXACT TIME (to the second)
     * $date can be a string, a JDate object or a placeholder ([today], [tomorrow], [yesterday])
     * @param $field
     * @param $date
     * @return $this
     */
    public function whereDateTime ($field, $operator, $date)
    {
        $date = \Zoolanders\Framework\Service\Date::getDateTime($date);
        $this->where($field, $operator, $date);

        return $this;
    }

    /**
     * Filter based on EXACT TIME (to the second)
     * $date can be a string, a JDate object or a placeholder ([today], [tomorrow], [yesterday])
     * @param $field
     * @param $date
     * @return $this
     */
    public function orWhereDateTime ($field, $operator, $date)
    {
        $date = \Zoolanders\Framework\Service\Date::getDateTime($date);
        $this->orWhere($field, $operator, $date);

        return $this;
    }
}
