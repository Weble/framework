<?php

namespace ZFTests\Event;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\TestEvent;
use Zoolanders\Framework\Event\Dispatcher;

/**
 * Class DispatcherTest
 * Event dispatcher class
 *
 * @package ZFTests\Event
 */
class DispatcherTest extends ZFTestCase
{
    public static $check = false;

    /**
     * Sample listening method
     */
    public static function listenerSample($event){
        self::$check = $event->getReturnValue();
    }

    /**
     * Test event triggering
     *
     * @covers      Dispatcher::trigger()
     * @depends     testNotify
     */
    public function testTriggerEvent(){
        $dispatcher = new Dispatcher(self::$container->zoo);
        $event = new TestEvent();

        $dispatcher->trigger($event);
        //$this->assertEventTriggered('classes:testevent', function($event){});
    }
}
