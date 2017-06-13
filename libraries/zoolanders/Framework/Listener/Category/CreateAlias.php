<?php

namespace Zoolanders\Framework\Listener\Category;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Event\Category\BeforeCopy;
use Zoolanders\Framework\Model\Category as CategoryModel;

/**
 * Class BeforeCopy
 * @package Zoolanders\Framework\Listener\Item
 */
class CreateAlias extends Listener
{
    /**
     * @var Item Model
     */
    protected $model;

    /**
     * BeforeCopy constructor.
     * @param ItemModel $model
     */
    public function __construct(CategoryModel $model){
        $this->model = $model;
    }

    /**
     * Before create item copy
     *
     * @param Copy $event
     *
     * @return void
     */
    public function handle(BeforeCopy $event){
        $category = $event->getCategory();
        $category->id = null;
        $category->alias = $this->model->generateAlias($category->alias);
        $category->name = 'Copy of ' . $category->name;
    }
}
