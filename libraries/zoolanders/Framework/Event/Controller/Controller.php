<?php

namespace Zoolanders\Framework\Event\Controller;

use Zoolanders\Framework\Event\Event;

abstract class Controller extends Event {
    /**
     * @var \Zoolanders\Framework\Controller\Controller
     */
    protected $controller;

    /**
     * BeforeExecute constructor.
     * @param \Zoolanders\Framework\Controller\Controller $controller
     */
    public function __construct (\Zoolanders\Framework\Controller\Controller $controller) {
        $this->controller = $controller;
    }

    /**
     * @return \Zoolanders\Framework\Controller\Controller
     */
    public function getController () {
        return $this->controller;
    }
}
