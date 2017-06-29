<?php

namespace Zoolanders\Framework\Event\Controller;

use Zoolanders\Framework\Controller\ControllerInterface;
use Zoolanders\Framework\Event\Event;

abstract class Controller extends Event
{
    /**
     * @var ControllerInterface
     */
    protected $controller;

    /**
     * BeforeExecute constructor.
     * @param ControllerInterface $controller
     */
    public function __construct (ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return ControllerInterface
     */
    public function getController ()
    {
        return $this->controller;
    }
}
