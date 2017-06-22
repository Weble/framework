<?php

namespace Zoolanders\Framework\Model\Item\Element;

trait Date {
    abstract public function getContainer ();

    abstract public function whereRaw ($sql);

    abstract public function getQuery ();

    abstract public function orWhereRaw ($sql);

    /**
     * date format yyyy-mm-dd
     * @var string
     */
    protected static $dateRegexp = '/^(19[0-9]{2}|2[0-9]{3})-(0[1-9]|1[012])-([123]0|[012][1-9]|31)/';

    /**
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function whereElementDate ($field, $value) {
        $wheres = $this->getWhereElementDate($field, $value);

        $this->whereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function orWhereElementDate ($field, $value) {
        $wheres = $this->getWhereElementDate($field, $value);

        $this->orWhereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function whereElementDateTime ($field, $value) {
        $wheres = $this->getWhereElementDateTime($field, $value);

        $this->whereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function orWhereElementDateTime ($field, $value) {
        $wheres = $this->getWhereElementDateTime($field, $value);

        $this->orWhereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereElementDateFrom ($field, $value) {
        $from = $this->getContainer()->date->getDayStart($value);

        $field = $this->getQuery()->qn($field);
        $from = $this->getQuery()->q($from);

        $this->whereRaw("( (SUBSTR({$field}, -19) >= {$from}) OR ({$from} <= SUBSTR({$field}, -19)) )");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function orWhereElementDateFrom ($field, $value) {
        $from = $this->getContainer()->date->getDayStart($value);

        $field = $this->getQuery()->qn($field);
        $from = $this->getQuery()->q($from);

        $this->orWhereRaw("( (SUBSTR({$field}, -19) >= {$from}) OR ({$from} <= SUBSTR({$field}, -19)) )");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereElementDateTo ($field, $value) {
        $to = $this->getContainer()->date->getDayEnd($value);

        $field = $this->getQuery()->qn($field);
        $to = $this->getQuery()->q($to);

        $this->whereRaw("( (SUBSTR({$field}, 1, 19) <= {$to}) OR ({$to} >= SUBSTR({$field}, 1, 19)) )");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function orWhereElementDateTo ($field, $value) {
        $to = $this->getContainer()->date->getDayEnd($value);

        $field = $this->getQuery()->qn($field);
        $to = $this->getQuery()->q($to);

        $this->orWhereRaw("( (SUBSTR({$field}, 1, 19) <= {$to}) OR ({$to} >= SUBSTR({$field}, 1, 19)) )");

        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function whereElementDateBetween ($field, $valueFrom, $valueTo) {
        $wheres = $this->getWhereElementDateBetween($field, $valueFrom, $valueTo);

        $this->whereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function orWhereElementDateBetween ($field, $valueFrom, $valueTo) {
        $wheres = $this->getWhereElementDateBetween($field, $valueFrom, $valueTo);

        $this->orWhereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return array
     */
    protected function getWhereElementDate ($field, $value) {
        $date = $this->getContainer()->date->getDateOnly($value);
        $from = $this->getContainer()->date->getDayStart($value);
        $to = $this->getContainer()->date->getDayEnd($value);

        $field = $this->getQuery()->qn($field);
        $date = $this->getQuery()->q($date);
        $from = $this->getQuery()->q($from);
        $to = $this->getQuery()->q($to);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$field} LIKE '%{$date}%')",
            "(SUBSTR({$field}, 1, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 1, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 1, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 21, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 41, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 41, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 61, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 81, 19) BETWEEN {$from} AND {$to})"
        ];
        return $wheres;
    }

    /**
     * @param $field
     * @param $value
     * @return array
     */
    protected function getWhereElementDateTime ($field, $value) {
        $date = $this->getContainer()->date->getDateOnly($value);
        $field = $this->getQuery()->qn($field);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$field} LIKE '%{$date}%')",
            "('{$date}' BETWEEN {$field} AND {$field}) AND {$field} NOT REGEXP '[[.LF.]]')"
        ];
        return $wheres;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return array
     */
    protected function getWhereElementDateBetween ($field, $valueFrom, $valueTo) {
        $from = $this->getContainer()->date->getDayStart($valueFrom);
        $to = $this->getContainer()->date->getDayEnd($valueTo);

        $field = $this->getQuery()->qn($field);
        $from = $this->getQuery()->q($from);
        $to = $this->getQuery()->q($to);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$from} BETWEEN SUBSTR({$field}, 1, 19) AND SUBSTR({$field}, -19))",
            "({$to} BETWEEN SUBSTR({$field}, 1, 19) AND SUBSTR({$field}, -19))",
            "(SUBSTR({$field}, 1, 19) BETWEEN $from AND $to)",
            "(SUBSTR({$field}, -19) BETWEEN {$from} AND {$to})"
        ];

        return $wheres;
    }
}
