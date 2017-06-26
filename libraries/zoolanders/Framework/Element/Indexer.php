<?php

namespace Zoolanders\Framework\Element;

use Zoolanders\Framework\Container\Container;

class Indexer
{
    /**
     * Available Element indexing types
     */
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TEXT = 'text';

    /**
     * @param \Element $element
     * @param array $items Can be both array of ids or array of Item
     */
    public function index (\Element $element, $items = [])
    {
        $this->cleanSearchIndex($element);

        $dataType = $this->getDataTypeFromElement($element);
        $values = $this->getValuesFromElement($element);

        if (!$items) {
            $items = [$element->getItem()->id];
        }

        $itemIds = [];
        foreach ($items as $item) {
            if ($item instanceof \Item) {
                $itemIds[] = $item->id;
            } else {
                $itemIds[] = $item;
            }
        }

        $db = $this->container->db;

        // save into the right table
        $table = '#__zoo_zl_search_' . $dataType;

        $dataValues = [];
        foreach ($itemIds as $id) {
            $dataValues[] = implode(",", [$db->q($id), $db->q($element->identifier), $db->q($values)]);
        }

        /** @var \JDatabaseQuery $query */
        $query = $db->getQuery(true);
        $query->insert($table)->columns(['item_id', 'element_id', 'value'])->values($dataValues);
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @param \Element $element
     */
    public function cleanSearchIndex (\Element $element)
    {
        $db = $this->container->db;

        $table = '#__zoo_zl_search_' . $this->getDataTypeFromElement($element);

        /** @var \JDatabaseQuery $query */
        $query = $db->getQuery(true);
        $query->delete()->from($table)->where('element_id = ' . (int)$element->identifier);
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @return array
     */
    public static function getAvailableDataTypes ()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        $constants = $oClass->getConstants();

        $values = [];

        foreach ($constants as $constant => $value) {
            if (stripos($constant, 'TYPE_') === 0) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @param \Element $element
     * @return array
     */
    public static function getValuesFromElement (\Element $element)
    {
        if (self::isRepeatable($element)) {
            $values = explode("\n", $element->getSearchData());
            return $values;
        }

        return [$element->getSearchData()];
    }

    /**
     * @param \Element $element
     * @return bool
     */
    public static function isRepeatable (\Element $element)
    {
        if ($element instanceof \ElementRepeatable) {
            return true;
        }

        if ($element instanceof \ElementRepeatablePro) {
            return true;
        }

        if ($element instanceof \ElementOption) {
            return true;
        }

        return false;
    }

    /**
     * @param \Element $element
     * @return string
     */
    public static function getDataTypeFromElement (\Element $element)
    {
        $type = $element->getElementType();

        switch ($type) {
            case 'rating':
                return self::TYPE_FLOAT;
            case 'text':
            case 'textpro':
            case 'select':
            case 'radio':
            case 'checkbox':
            case 'email':
                return self::TYPE_STRING;
                break;
            case 'date':
            case 'datepro':
                return self::TYPE_DATETIME;
                break;
            case 'textareapro':
            case 'textarea':
            default:
                return self::TYPE_TEXT;
                break;
        }
    }
}
