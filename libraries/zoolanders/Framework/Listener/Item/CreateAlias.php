<?php

namespace Zoolanders\Framework\Listener\Item;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Event\Item\BeforeCopy;
use Zoolanders\Framework\Model\Item as ItemModel;

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
    public function __construct(ItemModel $model){
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
        $item = $event->getItem();
        $item->id = null;
        $item->alias = $this->model->generateAlias($item->alias);
        $item->name = 'Copy of ' . $item->name;
    }
}
