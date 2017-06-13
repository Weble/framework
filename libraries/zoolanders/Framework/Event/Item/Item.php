<?php

namespace Zoolanders\Framework\Event\Item;

abstract class Item extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Item
     */
    protected $item;

    /**
     * Beforesave constructor.
     * @param \Item $item
     */
    public function __construct(\Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return \Item
     */
    public function getItem()
    {
        return $this->item;
    }
}
