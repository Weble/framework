<?php

namespace Zoolanders\Framework\Event\Controller;

class BeforeExecute extends Controller
{
    /**
     * @var string
     */
    protected $task;

    /**
     * BeforeExecute constructor.
     * @param \Zoolanders\Framework\Controller\Controller $controller
     * @param $task
     */
    public function __construct (\Zoolanders\Framework\Controller\Controller $controller, $task)
    {
        parent::__construct($controller);

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
