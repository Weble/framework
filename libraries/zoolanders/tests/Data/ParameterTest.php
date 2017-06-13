<?php

namespace ZFTests\Data;

use Zoolanders\Framework\Data\Parameter;

/**
 * Class ParameterTest
 * Parameter data unit tests
 *
 * @package ZFTests\Data
 */
class ParameterTest extends DataTest
{
    /**
     * Make dataset for forward testing:
     */
    protected function makeDataSet($dataset){

        $this->object = new Parameter($dataset);

        return $this->object;
    }

    /**
     * Test loadArray method
     *
     * @covers          Parameter::loadArray()
     * @dataProvider    arrayDataSet
     */
    public function testLoadArray($dataset, $control_value){
        $this->makeDataSet([]);
        $this->object->loadArray($dataset);

        // Check if control element is present:
        $this->assertTrue($this->object->has($control_value));
    }

    /**
     * Test loadObject method
     *
     * @covers          Parameter::loadObject()
     * @dataProvider    objectDataSet
     */
    public function testLoadObject($dataset, $control_prop, $control_value){
        $this->makeDataSet([]);
        $this->object->loadObject($dataset);

        // Check if control element is present and value is correct:
        $this->assertTrue($this->object->has($control_prop));
        $this->assertEquals($control_value, $this->object->get($control_prop));
    }

    /**
     * Object dataset
     */
    public function objectDataSet(){

        $object = new \stdClass();
        $object->a = 'alpha';
        $object->b = 'bravo';
        $object->c = 'charlie';

        return [
          [ $object, 'a', 'alpha' ],
          [ $object, 'b', 'bravo' ],
          [ $object, 'c', 'charlie' ]
        ];
    }

 }
