<?php

namespace Zoolanders\Framework\Model\Item;

trait Tags
{
    abstract public function wherePrefix ($sql);

    abstract public function getQuery ();

    public function filterTags ($tags)
    {
        settype($categories, 'array');

        $tags = $this->getQuery()->q($tags);

        foreach ($tags as $tag) {
            $this->whereRaw($this->getTablePrefix() . ".id IN ("
                . " SELECT ti.id FROM " . ZOO_TABLE_ITEM . " AS ti"
                . " LEFT JOIN " . ZOO_TABLE_TAG . " AS t"
                . " ON ti.id = t.item_id"
                . " WHERE t.name = " . $tag . ")"
            );
        }
    }

    public function filterTagsIn ($tags)
    {
        settype($categories, 'array');

        $this->join(ZOO_TABLE_TAG, $this->getTablePrefix() . '.id = t.item_id', 't');
        $this->orWhereRaw("t.name IN (" . implode(',', $this->getQuery()->q($tags)) . ")");
    }
}
