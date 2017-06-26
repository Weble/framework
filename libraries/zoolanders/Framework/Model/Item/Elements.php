<?php

namespace Zoolanders\Framework\Model\Item;

use Zoolanders\Framework\Element\Indexer;

trait Elements
{
    protected $elements = [];

    protected $types = [];

    protected $elementJoins = 0;

    /**
     * @return array
     */
    public function getTypes ()
    {
        if (!$this->types) {
            $groups = $this->zoo->getApp()->application->groups();
            foreach ($groups as $group) {
                $this->types = array_merge($this->types, $group->getTypes());
            }
        }

        return $this->types;
    }

    /**
     * @param $id
     * @return bool
     */
    public function getElement ($id)
    {
        if (!isset($this->elements[$id])) {
            /** @var \Type $type */
            foreach ($this->getTypes() as $type) {
                if ($element = $type->getElement($id)) {
                    $this->elements[$id] = $element;
                }
            }
        }

        if (!isset($this->elements[$id])) {
            return false;
        }

        return $this->elements[$id];
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function whereElement ($id, $operator, $value, $convert = false)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id, $alias);

        // Decimal conversion fix
        if (strtoupper($convert) == 'DECIMAL') {
            $convert = 'DECIMAL(10,2)';
        }

        // validate value to be sure it is number
        if (strtoupper($convert) == 'SIGNED') {
            $value = floatval($value);
        }

        $this->whereRaw($alias . '.element_id = ' . $this->getQuery()->q($id));

        if ($convert) {
            $this->where("CONVERT(TRIM( " . $this->getQuery()->qn($alias . '.value') . "+0), {$convert})", $operator, $value);
            return;
        }

        $this->whereRaw($alias . '.value  = ' . $value);

        return $this;
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function whereAnyElement ($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->where($this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->whereAny("TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);

        return $this;
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function orWhereAnyElement ($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->where($this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->orWhereAny("TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);

        return $this;
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function orWhereElement ($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->orWhere($this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->orWhere("TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);

        return $this;
    }

    /**
     * @param $id
     * @param $values
     */
    public function whereElementHasAll ($id, $values)
    {
        settype($values, 'array');

        $alias = $this->getJoinElementAlias();
        $this->joinElement($id);

        $this->where($this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));

        // Normal selects / radio / etc (ElementOption)
        $multiples = $this->getMultipleElementValues($values, $alias);

        $this->whereRaw('(' . implode(" AND ", $multiples));

        return $this;
    }

    /**
     * @param $id
     * @param $values
     */
    public function whereElementHasAny ($id, $values)
    {
        settype($values, 'array');

        $alias = $this->getJoinElementAlias();
        $this->joinElement($id);

        $this->where($this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $multiples = $this->getMultipleElementValues($values, $alias);


        $this->whereRaw('(' . implode(" OR ", $multiples));

        return $this;
    }

    /**
     * @param $id
     */
    public function joinElement ($id, $alias = null)
    {
        if (!$alias) {
            $alias = $this->getJoinElementAlias();
        }

        // new join here
        $this->elementJoins++;
        $element = $this->getElement($id);

        $dataType = Indexer::getDataTypeFromElement($element);

        if ($element) {
            $this->join(
                '#__zoo_zl_search_' . $dataType,
                $this->getQuery()->qn($this->getTablePrefix() . ".id") . ' = ' . $this->getQuery()->qn($alias . '.item_id') .
                ' AND ' . $this->getQuery()->qn($alias . '.element_id') . ' = ' . $this->getQuery()->q($id),
                $alias
            );
        }
    }

    /**
     * @return string
     */
    protected function getJoinElementAlias ()
    {
        return 'b' . $this->elementJoins;
    }

    /**
     * @param $values
     * @param $alias
     * @return array
     */
    protected function getMultipleElementValues ($values, $alias)
    {
        $multiples = [];

        // Normal selects / radio / etc (ElementOption)
        foreach ($values as $value) {
            $multiple = "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim($this->getQuery()->escape($value))) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim($this->getQuery()->escape($value) . "\n%")) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim($this->getQuery()->escape($value) . ' %')) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim("%\n" . $this->getQuery()->escape($value))) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim("%\n" . $this->getQuery()->escape($value) . "\n%"));
            $multiples[] = "(" . $multiple . ")";
        }

        return $multiples;
    }
}
