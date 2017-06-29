<?php

namespace Zoolanders\Framework\Event\Controller;

use Zoolanders\Framework\Controller\ControllerInterface;

class BeforeExecute extends Controller
{
    /**
     * @var string
     */
    protected $task;

    /**
     * BeforeExecute constructor.
     * @param ControllerInterface $controller
     * @param $task
     */
    public function __construct (ControllerInterface $controller, $task)
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
