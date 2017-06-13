<?php

namespace ZFTests\Utils;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Utils\IsString;

/**
 * Class UtilsTest
 * Utility helper classes tests
 *
 * @package ZFTests\Utils
 */
class UtilsTest extends ZFTestCase
{
    use IsString;

    /**
     * Check if provided bvalue is a string
     *
     * @covers          IsString::isString()
     * @dataProvider    dataSet
     */
    public function testIsString($value, $expected)
    {
        // Check against isString method:
        $this->assertEquals($expected, $this->isString($value));
    }

    /**
     * IsString testing dataset
     */
    public function dataSet()
    {

        $obj = new \stdClass();

        return [
            ['alpha', true],
            ['42', true],
            [42, false],
            [0.56, false],
            [null, false],
            [[1, 2, 3], false],
            [$obj, false]
        ];
    }

    /**
     * Testing record set
     */
    public function recordSetData()
    {
        return [
            [
                [
                    ['name' => 'John', 'surname' => 'Doe'],
                    ['name' => 'Peter', 'surname' => 'Parker'],
                    ['name' => 'Kent', 'surname' => 'Clark']
                ],
                'name',
                ['John', 'Peter', 'Kent']
            ],
            [
                [
                    ['letter' => 'A', 'code' => 'alpha'],
                    ['letter' => 'B', 'code' => 'bravo'],
                    ['letter' => 'C', 'code' => 'charlie']
                ],
                'code',
                ['alpha', 'bravo', 'charlie']
            ]
        ];
    }
}
