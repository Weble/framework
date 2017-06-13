<?php

namespace ZFTests\Classes;

/**
 * Class EventNode
 * Describes event node in event stack
 */
class EventNode {

    /**
     * @var string  Event name
     */
    public $name = '';

    /**
     * @var null    Event object
     */
    public $event = null;

    /**
     * EventNode constructor.
     *
     * @param $name
     * @param null $event
     */
    public function __construct($name, $event = null)
    {
        $this->name = $name;
        $this->event = $event;
    }
}
