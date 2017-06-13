<?php

namespace ZFTests\Classes;

/**
 * Class Dataset
 * @package ZFTests\Classes
 */
trait Dataset
{
    /**
     * @var Testable data object
     */
    protected $object;

    /**
     * Test Get, Set and Has calls
     *
     * @param $control_value
     */
    public function setGetHas($control_value){

        $this->assertTrue($this->object->has($control_value));

        // Test getters / setters:
        $this->object->set('f', 'foxtrot');
        $this->assertTrue($this->object->has('f'));
        $this->assertEquals('foxtrot', $this->object->get('f'));

        // Test "magic" getter/setter:
        $this->object->e = 'echo';
        $this->assertTrue($this->object->has('e'));
        $this->assertEquals('echo', $this->object->get('e'));
    }

    /**
     * Test removing
     *
     * @param $control_value
     */
    public function removing($control_value){

        $this->assertTrue($this->object->has($control_value));

        // Removing element from the dataset:
        $this->object->remove($control_value);
        $this->assertFalse($this->object->has($control_value));
    }

    /**
     * Check finding param
     *
     * @param $needle
     * @param $expected
     */
    public function finding($needle, $expected){

        // Check if we found expected value:
        $this->assertEquals($expected, $this->object->find($needle, 'zulu'));
    }

    /**
     * Flatterning
     *
     * @param $expected
     */
    public function flatterning($expected){

        // Check if transformation was correct:
        $this->assertEquals($expected, $this->object->flattenRecursive());
    }
}
