<?php

namespace Zoolanders\Framework\Event\Item;

use Zoolanders\Framework\Event\HasSubjectInterface;

abstract class Item extends \Zoolanders\Framework\Event\Event implements HasSubjectInterface
{
    /**
     * @var \Item
     */
    protected $item;

    /**
     * Beforesave constructor.
     * @param \Item $item
     */
    public function __construct (\Item $item = null)
    {
        $this->item = $item;
    }

    /**
     * @return \Item
     */
    public function getItem ()
    {
        return $this->item;
    }
}
