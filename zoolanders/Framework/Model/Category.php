<?php
/**
 * Created by PhpStorm.
 * User: dimmask
 * Date: 10.04.17
 * Time: 11:58
 */

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Model\Database\UniqueAlias;

class Category extends Database
{
    use UniqueAlias;

    protected $tablePrefix = 'c';
    protected $tableName = ZOO_TABLE_CATEGORY;
    protected $entityClass = 'Category';
    protected $tableClassName = 'category';

    protected $cast = [
        'params' => 'json'
    ];

}
