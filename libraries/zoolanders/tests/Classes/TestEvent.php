<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Event\Event;

/**
 * Class TestEvent
 * Test Class
 *
 * @package ZFTests\Classes
 */
class TestEvent extends Event
{
    /**
     * TestEvent constructor.
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }
}
