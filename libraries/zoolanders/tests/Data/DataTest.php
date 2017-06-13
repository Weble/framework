<?php

namespace ZFTests\Data;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Data\Data;
use ZFTests\Classes\Dataset;

/**
 * Class DataTest
 * Data struct unit test
 *
 * @package ZFTests\Data
 */
class DataTest extends ZFTestCase
{
    use Dataset;

    /**
     * Make dataset for forward testing:
     */
    protected function makeDataSet($dataset){

        $this->object = new Data($dataset);

        return $this->object;
    }

    /**
     * Data asseting test
     *
     * @covers  Data::create()
     * @covers  Data::get()
     * @covers  Data::set()
     * @covers  Data::has()
     * @dataProvider arrayDataSet
     */
    public function testGetSetHas($dataset, $control_value){

        $this->makeDataSet($dataset);
        $this->setGetHas($control_value);
    }

    /**
     * Test removing
     *
     * @depends         testGetSetHas
     * @covers          Data::remove()
     * @dataProvider    arrayDataSet
     */
    public function testRemove($dataset, $control_value){

        $this->makeDataSet($dataset);
        $this->removing($control_value);
    }

    /**
     * Test search function
     *
     * @covers          Data::find()
     * @dataProvider    lookupDataSet
     */
    public function testFind($dataset, $needle, $expected){

        $this->makeDataSet($dataset);
        $this->finding($needle, $expected);
    }

    /**
     * Test search recursive method
     *
     * @covers          Data::searchRecursive()
     * @dataProvider    lookupRecursiveDataSet
     */
    public function testSearchRecursive($dataset, $expected, $needle){

        $this->makeDataSet($dataset);
        $this->assertEquals($expected, $this->object->searchRecursive($needle));
    }

    /**
     * Test flattern array
     *
     * @covers          Data::flattenRecursive()
     * @dataProvider    flatternDataSet
     */
    public function testFlattern($dataset, $expected){

        $this->makeDataSet($dataset);
        $this->flatterning($expected);
    }

    /**
     * Array Data provider
     */
    public function arrayDataSet(){
        return [
            [
                [1, 2, 3],
                2
            ],
            [
                ['a' => 'alpha', 'b' => 'bravo', 'c' => 'charlie'],
                'b'
            ]
        ];
    }

    /**
     * Data provider for search/find ops testing:
     */
    public function lookupDataSet(){
        return [
            [
                [1,2,3],
                2,
                3
            ],
            [
                [
                    'a' => 'alpha',
                    'b' => 'bravo',
                    'c' => 'charlie'
                ],
                'b',
                'bravo'
            ],
            [
                ['squad' =>
                    [
                        'corporal' => 'John',
                        'sergeant' => 'Peter',
                        'captain' => 'Jack'
                    ]
                ],
                'squad.captain',
                'Jack'
            ],
            [
                [1,2,3],
                3,
                'zulu'
            ]
        ];
    }

    /**
     * Data provider for recursive search/find ops testing:
     */
    public function lookupRecursiveDataSet(){
        return [
            [
                [
                    'a' => 'alpha',
                    'b' => ['foo' => 'bravo'],
                    'c' => 'charlie'
                ],
                'b',
                'bravo'
            ],
            [
                ['squad' =>
                    [
                        'corporal' => 'John',
                        'sergeant' => 'Peter',
                        'captain' => 'Jack'
                    ]
                ],
                'squad',
                'Jack'
            ],
            [
                [1,2,3],
                false,
                4
            ]
        ];
    }

    /**
     * Flattern methods testing data set
     */
    public function flatternDataSet(){
        return [
            [
                [
                    [1, 2, 3], 4, 5
                ],
                [
                    1, 2, 3, 4, 5
                ]
            ],
            [
                ['a' => 'alpha', 'b' => 'bravo'],
                ['alpha', 'bravo']
            ]
        ];
    }
}
