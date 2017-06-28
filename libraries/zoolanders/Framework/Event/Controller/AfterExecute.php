<?php

namespace Zoolanders\Framework\Event\Controller;

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
     * @param \Zoolanders\Framework\Controller\Controller $controller
     * @param $task
     * @param $response
     */
    public function __construct (\Zoolanders\Framework\Controller\Controller $controller, $task, $response)
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
