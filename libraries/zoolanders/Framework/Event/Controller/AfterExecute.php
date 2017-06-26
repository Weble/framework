<?php

namespace Zoolanders\Framework\Event\Controller;

class AfterExecute extends Controller
{
    /**
     * @var string
     */
    protected $task;

    /**
     * AfterExecute constructor.
     * @param \Zoolanders\Framework\Controller\Controller $controller
     * @param $task
     */
    public function __construct (\Zoolanders\Framework\Controller\Controller $controller, $task)
    {
        $this->controller = $controller;
        $this->task = $task;
    }

    /**
     * @return string
     */
    public function getTask ()
    {
        return $this->task;
    }
}
