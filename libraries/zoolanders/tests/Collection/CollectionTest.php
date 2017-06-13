<?php

namespace ZFTests\Collection;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Collection\Collection;

/**
 * Class CollectionTest
 * Collection class tests
 *
 * @package ZFTests\Collection
 */
class CollectionTest extends ZFTestCase
{
    /**
     * Test creating collection from an array
     *
     * @dataProvider    dataProvider
     * @covers          Collection::make()
     * @covers          Collection::all()
     */
    public function testMakeCollection($items){
        $collection = Collection::make($items);

        //Check if all the items are present in the resulting collection:
        $this->assertArraySubset($items, $collection->all());
    }

    /**
     * Test collapsing the collection
     *
     * @depends         testMakeCollection
     * @dataProvider    collapseDataSet
     * @covers          Collection::collapse()
     */
    public function testCollapse($src, $expected){
        $collection = Collection::make($src);

        // Check if items array is transformed correct:
        $this->assertArraySubset( $expected, $collection->collapse()->all());
    }

    /**
     * Test collection diff calculation
     *
     * @depends         testMakeCollection
     * @dataProvider    diffDataSet
     * @covers          Collection::diff()
     */
    public function testCollectionDiff($cmp1, $cmp2, $expected){
        $comparison_collection1 = new Collection($cmp1);

        $diff = $comparison_collection1->diff($cmp2);

        $this->assertArraySubset($expected, $diff->all());
    }

    /**
     * Test each method for collections
     *
     * @depends         testMakeCollection
     * @dataProvider    eachDataSet
     * @covers          Collection::each()
     */
    public function testCollectionEach($callback, $src, $expected){
        $collection = Collection::make($src);
        $updated = $collection->each($callback);

        $this->assertArraySubset($expected, $updated->all());
    }

    /**
     * Data provider for collection ops testing
     */
    public function dataProvider(){

        return [
            [
                [1, 2, 3]
            ],
            [
                ["one", "two", "three"]
            ]
        ];
    }

    /**
     * Array data provider for collection ops testing
     */
    public function collapseDataSet(){

        return [
            [
                [[0, 1], [2, 3], [4, 5]],
                [0, 1, 2, 3, 4, 5]
            ]
        ];
    }

    /**
     * Diff data sets provider
     */
    public function diffDataSet(){
        return [
            [
                [1, 2, 3],
                [2, 3, 4],
                [1]
            ],
            [
                ["green", "blue", "red"],
                ["red"],
                ["green", "blue"]
            ]
        ];
    }

    /**
     * Each function datasets
     */
    public function eachDataSet(){
        return [
            [
                function($arg){
                    return $arg * 10;
                },
                [1, 2, 3],
                [10, 20, 30]
            ],
            [
                function($arg){
                    return '_' . $arg;
                },
                ["one", "two"],
                ["_one", "_two"]
            ]
        ];
    }
}
