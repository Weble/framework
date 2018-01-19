<?php

namespace Zoolanders\Framework\Event\Category;

use Zoolanders\Framework\Event\HasSubjectInterface;

class Category extends \Zoolanders\Framework\Event\Event implements HasSubjectInterface
{
    /**
     * @var \Category
     */
    protected $category;

    /**
     * Beforesave constructor.
     * @param \Category $category
     */
    public function __construct (\Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return \Category
     */
    public function getCategory ()
    {
        return $this->category;
    }
}
