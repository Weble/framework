<?php

namespace ZFTests\Listener;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Listener\Listener;

/**
 * Class ListenerTest
 * Listener class tests
 *
 * @package ZFTests\Listener
 */
class ListenerTest extends ZFTestCase
{
    /**
     * Test listener
     */
    public function testListener(){
        $this->markTestSkipped('Listener is abstract and have no any core logic for now');
    }
}
