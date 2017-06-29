<?php

namespace Zoolanders\Framework\Dispatcher;

use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Controller\ControllerInterface;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Service\Event;

class Dispatcher
{
    /**
     * Default id field parameter
     */
    CONST PARAM_ID = 'id';

    /**
     * Determines the CRUD task to use based on the view name and HTTP verb used in the request.
     * @credits https://github.com/akeeba/fof/blob/development/fof/Controller/DataController.php
     *
     * @return  string  The CRUD task (browse, read, edit, delete)
     */
    public function getCrudTask (ControllerInterface $controller, Request $request)
    {
        $task = $controller->getDefaultTask();
        $id = $request->get(self::PARAM_ID);

        // Alter the task based on the verb
        switch ($request->getHttpVerb()) {
            case 'POST':
            case 'PUT':
                $task = ControllerInterface::TASK_SAVE;
                break;
            case 'DELETE':
                if ($id) {
                    $task = ControllerInterface::TASK_DELETE;
                }
                break;
            case 'GET':
            default:
                if ($id) {
                    $task = ControllerInterface::TASK_READ;
                }
                break;
        }

        return $task;
    }

    /**
     * @param ControllerInterface $controller
     * @return ControllerInterface
     */
    public function getController(ControllerInterface $controller)
    {
        return $controller;
    }

    /**
     * @param ControllerInterface $controller
     * @param Request $request
     * @return string
     */
    public function getTask(ControllerInterface $controller, Request $request)
    {
        $crudTask = $this->getCrudTask($controller, $request);

        return $request->getCmd('task', $crudTask);
    }
}
