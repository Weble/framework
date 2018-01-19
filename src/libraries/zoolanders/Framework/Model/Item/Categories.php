<?php

namespace Zoolanders\Framework\Model\Item;

trait Categories
{
    public function filterCategories ($categories)
    {
        settype($categories, 'array');

        // it's heavy query but the only way for AND mode
        foreach ($categories as $id) {

            if ($id instanceof \Category) {
                $id = $id->id;
            }

            $this->whereRawPrefix(
                "id IN ("
                . " SELECT b.id FROM " . ZOO_TABLE_ITEM . " AS b"
                . " LEFT JOIN " . ZOO_TABLE_CATEGORY_ITEM . " AS y"
                . " ON b.id = y.item_id"
                . " WHERE y.category_id = " . (int)$id . ")"
            );
        }
    }

    public function filterCategoriesIn ($categories)
    {
        settype($categories, 'array');

        $this->join(ZOO_TABLE_CATEGORY_ITEM, $this->getTablePrefix() . '.id = c.item_id', "c");
        $this->whereRaw("c.category_id IN (" . implode(',', $this->getQuery()->q($categories)) . ")");
    }
}
