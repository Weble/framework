<?php

namespace ZFTests\Dispatcher;

use ZFTests\Classes\MockRequest;
use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Dispatcher\Dispatcher;
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Request\RequestInterface;

/**
 * Class DispatcherTest
 * Test dispatching workflow
 */
class DispatcherTest extends ZFTestCase
{
    /**
     * Test dispatching front controller
     *
     * @covers      Dispatcher::dispatch()
     * @expectedException \Zoolanders\Framework\Dispatcher\Exception\ControllerNotFound
     * @skip
     */
    public function testInvalidControllerException ()
    {
        $this->markTestSkipped(
            'Does not work yet'
        );
        return;

        $request = new MockRequest();
        /** @var Dispatcher $dispatcher */
        self::$container->dispatch($request);

        // Check if expected events were triggered
        /*$this->assertEventTriggered('dispatcher:beforedispatch', function () {
        });
        $this->assertEventTriggered('dispatcher:afterdispatch', function () {
        });*/
    }
}
