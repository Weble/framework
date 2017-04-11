<?php
namespace Zoolanders\Framework\Event\Category;

class BeforeCopy extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Category
     */
    protected $category;

    /**
     * Beforesave constructor.
     * @param \Item $item
     */
    public function __construct(\Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return \Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
