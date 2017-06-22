<?php

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Model\Database\UniqueAlias;

class Category extends Database {
    use UniqueAlias;

    protected $tablePrefix = 'c';
    protected $tableName = ZOO_TABLE_CATEGORY;
    protected $entityClass = 'Category';
    protected $tableClassName = 'category';

    protected $cast = [
        'params' => 'json'
    ];

}
