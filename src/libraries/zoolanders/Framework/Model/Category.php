<?php

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Model\Category\Basics;
use Zoolanders\Framework\Model\Database\UniqueAlias;

/**
 * Class Category
 * @package Zoolanders\Framework\Model
 */
class Category extends Database
{
    use UniqueAlias, Basics;

    /**
     * @var string
     */
    protected $tablePrefix = 'c';
    /**
     * @var string
     */
    protected $tableName = ZOO_TABLE_CATEGORY;
    /**
     * @var string
     */
    protected $entityClass = 'Category';
    /**
     * @var string
     */
    protected $tableClassName = 'category';

    /**
     * @var array
     */
    protected $cast = [
        'params' => 'json'
    ];

    /**
     * @param \Category $category
     * @param array $items
     */
    public function associate (\Category $category, $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof \Item){
                $item = $this->zoo->table->item->get($item);
            }

            $this->zoo->category->saveCategoryItemRelations($item, [$category->id]);
        }
    }

    /**
     * @param \Category $category
     * @param array $items
     */
    public function attach (\Category $category, $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof \Item){
                $item = $this->zoo->table->item->get($item);
            }

            $categories = $item->getRelatedCategoryIds(false);
            $categories[] = $category->id;


            $this->zoo->category->saveCategoryItemRelations($item, $categories);
        }
    }

    /**
     * @param \Category $category
     * @param array $items
     */
    public function detach (\Category $category, $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof \Item){
                $item = $this->zoo->table->item->get($item);
            }

            $categories = $item->getRelatedCategoryIds(false);
            $categories = array_diff($categories, [$category->id]);

            $this->zoo->category->saveCategoryItemRelations($item, $categories);
        }
    }
}
