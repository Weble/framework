<?php

namespace ZFTests\Model;

use Zoolanders\Framework\Model\Item;

/**
 * Class ItemTest
 * Item test
 *
 * @package ZFTests\Model
 */
class ItemTest extends DatabaseTest
{
    /**
     * @var string  Test cases source relative path
     */
    protected $_data_source = '/querying/item.csv';

    /**
     * Creates and returns instance of testing class
     */
    protected function getTestInstance(){

        return new Item(self::$container->db, self::$container->zoo);
    }

    /**
     * Test database selecting records from fixture set using query builder
     */
    public function testSelectingItem(){

        $dbm = $this->getTestInstance();
        $dbm->where('alias', 'LIKE', 'test-item-fixture');
        $dbm->buildQuery();

        $db = self::$container->db;
        $db->setQuery($dbm->getQuery());

        $this->assertArraySubset(['alias'=>'test-item-fixture','type'=>'article'], $db->loadAssoc());
    }

    /**
     * Fieldset provider
     */
    public function fieldsetProvider(){
        return [
            [ ['id'], "SELECT `a`.`id`FROM `#__zoo_item` AS `a`" ],
            [ ['id','alias'], "SELECT `a`.`id`,`a`.`alias`FROM `#__zoo_item` AS `a`" ]
        ];
    }

    /**
     * Test prefix data provider
     */
    public function prefixDataProvider(){
        return [
            ['a', 'SELECT `a`.*FROM `#__zoo_item` AS `a`WHERE `a`.`id` = 1'],
            ['b', 'SELECT `b`.*FROM `#__zoo_item` AS `b`WHERE `b`.`id` = 1'],
            ['c', 'SELECT `c`.*FROM `#__zoo_item` AS `c`WHERE `c`.`id` = 1']
        ];
    }
}
