<?php

namespace ZFTests\Event;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Event\Event;
use ZFTests\Classes\TestEvent;

/**
 * Class EventTest
 * Event class unit tests
 *
 * @package ZFTests\Event
 */
class EventTest extends ZFTestCase
{
    /**
     * Test get name func
     *
     * @covers          Event::getName()
     * @dataProvider    eventDataSet
     */
    public function testGetName($instance, $expected){
        if($instance instanceof Event){
            $this->assertEquals($expected, $instance->getName());
        } else {
            $this->markTestSkipped('Invalid event class instance provided');
        }
    }

    /**
     * Test get set value ops
     *
     * @covers          Event::setReturnValue()
     * @covers          Event::getReturnValue()
     * @dataProvider    valuesDataSet
     */
    public function testGetSetValue($control_value){
        $event = new TestEvent();
        $event->setReturnValue($control_value);

        $this->assertEquals($control_value, $event->getReturnValue());
    }

    /**
     * Test get initial data method
     *
     * @covers          Event::getProperties()
     */
    public function testGetProperties(){
        $event = new TestEvent( [ 'alpha' ] );
        $properties = $event->getProperties();

        $this->assertArrayHasKey('data', $properties);
        $this->assertArraySubset([ 'data' => ['alpha'] ], $properties);
    }

    /**
     * Provides event classes instances
     */
    public function eventDataSet(){
        return [
            [ new TestEvent(), 'classes:testevent' ]
        ];
    }

    /**
     * Provides control values
     */
    public function valuesDataSet(){
        return [
            [ 1 ],
            [ 'alpha' ],
            [ true ]
        ];
    }
}
