<?php

namespace ZFTests\Model;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Model\Model;
use Zoolanders\Framework\Container\Container;

/**
 * Class ModelTest
 * Unit tests for testing FW model class
 *
 * @package ZFTests\Model
 */
class ModelTest extends ZFTestCase
{
    /**
     * Get Set state operations check
     *
     * @covers          Model::setState()
     * @covers          Model::getState()
     * @covers          Model::__get()
     * @dataProvider    plainDataSet
     */
    public function testGetSetState($key, $value){

        $model = new Model(self::$container);
        $model->setState($key, $value);

        if(is_array($value)){
            // Test getter:
            $this->assertArraySubset($value, $model->getState($key));
            // Test "magic" getter:
            $this->assertArraySubset($value, $model->{$key});
        } elseif(is_object($value)) {
            $this->assertEquals(spl_object_hash($value), spl_object_hash($model->getState($key)));
            $this->assertEquals(spl_object_hash($value), spl_object_hash($model->{$key}));
        } else {
            $this->assertEquals($value, $model->getState($key));
            $this->assertEquals($value, $model->{$key});
        }
    }

    /**
     * Test clearState method
     *
     * @covers          Model::__set()
     * @covers          Model::clearState()
     * @dataProvider    plainDataSet
     */
    public function testClearState($key, $value){
        $model = new Model(self::$container);
        $model->{$key} = $value;

        $this->assertNotEmpty($model->getState($key));

        $model->clearState();

        $this->assertNull($model->getState($key));
    }

    /**
     * Test filtering for getState operation
     *
     * @depends         testGetSetState
     * @dataProvider    filteringDataSet
     */
    public function testGetStateFilter($filter, $testset, $expected){
        $model = new Model(self::$container);

        for($i=0; $i<count($testset); $i++){
            $model->setState($filter, $testset[$i]);
            $this->assertEquals($expected[$i], $model->getState($filter, null, $filter),
                'Expected that '.strtoupper($filter).' filter of '.$testset[$i].' is '.$expected[$i]);
        }
    }

    /**
     * KVP Data provider for model testing
     */
    public function plainDataSet(){

        return [
            [ 'a', 'alpha'],
            [ 'b', 'bravo'],
            [ 'c', 12],
            [ 'd', [1, 2, 3] ],
            [ 'e', new \stdClass() ]
        ];
    }

    /**
     * Testing set for testing getState filtering option
     */
    public function filteringDataSet(){
        // Relying on Joomla\Filter\InputFilter
        return [
            [ 'INT',        [1, '1', 'a'], [1, 1, 0] ],
            [ 'UINT',       [-2, '1', 'a'], [2, 1, 0] ],
            [ 'FLOAT',      [-2, '1.0', 'a'], [-2, 1, 0] ],
            [ 'BOOLEAN',    [true, 1, 'a', 0], [true, true, true, false] ],
            [ 'WORD',       ['ALPHA_123', 1, 'Sapienti Sat.,!-?\/*|'], ['ALPHA_', '', 'SapientiSat']],
            [ 'ALNUM',      ['ALPHA_123', 1, 'Sapienti Sat.,!-?\/*|'], ['ALPHA123', '1', 'SapientiSat']],
            [ 'CMD',        ['ALPHA_123', 1, 'Sapienti Sat.,!-?\/*|'], ['ALPHA_123', '1', 'SapientiSat.-']],
            [ 'BASE64',     [base64_encode('test'), '-+*/=()|,.'], [base64_encode('test'), '+/=']],
            [ 'STRING',     ['Lorem Ipsum <b>Dolor</b>'], ['Lorem Ipsum Dolor'] ],
            [ 'HTML',       ['Lorem Ipsum <b>Dolor</b>'], ['Lorem Ipsum Dolor'] ],
            [ 'PATH',       ['/../tmp/file_name.log', '||test', 'Sapienti Sat'], ['/../tmp/file_name.log', '', ''] ],
            [ 'TRIM',       [' test', 'Sapienti Sat '], ['test', 'Sapienti Sat'] ],
            [ 'RAW',        ['alpha', 100, '|+-*/<b>Test</b>&?()'], ['alpha', 100, '|+-*/<b>Test</b>&?()']],
            [ 'unk',        ['Lorem Ipsum <b>Dolor</b>'], ['Lorem Ipsum Dolor'] ],
        ];
    }
}
