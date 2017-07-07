<?php

namespace Zoolanders\Framework\Event\Controller;

use Zoolanders\Framework\Controller\ControllerInterface;

class AfterExecute extends Controller
{
    /**
     * @var string
     */
    protected $task;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * AfterExecute constructor.
     * @param ControllerInterface $controller
     * @param $task
     * @param $response
     */
    public function __construct (ControllerInterface $controller, $task, $response)
    {
        parent::__construct($controller);

        $this->task = $task;

        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getTask ()
    {
        return $this->task;
    }

    /**
     * @param $response
     */
    public function setResponse ($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse ()
    {
        return $this->response;
    }
}
