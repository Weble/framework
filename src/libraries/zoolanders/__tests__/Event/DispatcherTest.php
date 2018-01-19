<?php

namespace ZFTests\Event;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\TestEvent;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Dispatcher;
use Zoolanders\Framework\Event\MockDispatcher;
use Zoolanders\Framework\Event\Triggerable;

/**
 * Class DispatcherTest
 * Event dispatcher class
 *
 * @package ZFTests\Event
 */
class DispatcherTest extends ZFTestCase
{
    use Triggerable;

    public static $check = false;

    /**
     * Sample listening method
     */
    public static function listenerSample ($event)
    {
        self::$check = $event->getReturnValue();
    }

    /**
     * Test event triggering
     *
     * @covers      Dispatcher::trigger()
     */
    public function testTriggerEvent ()
    {
        $event = new TestEvent();

        Container::getInstance()->event->connect('test', function($triggeredEvent) use ($event) {
            $this->assertEquals($event, $triggeredEvent);
        });

        $this->triggerEvent($event);
    }
}
