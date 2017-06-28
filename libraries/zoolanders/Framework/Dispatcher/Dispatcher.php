<?php

namespace Zoolanders\Framework\Dispatcher;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Event\Controller\AfterExecute;
use Zoolanders\Framework\Event\Controller\BeforeExecute;
use Zoolanders\Framework\Event\Dispatcher\AfterDispatch;
use Zoolanders\Framework\Event\Dispatcher\BeforeDispatch;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\Service\Environment;
use Zoolanders\Framework\Service\Event;

class Dispatcher
{
    /**
     * Trigger Events
     */
    use Triggerable;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string  Default controller
     */
    protected $default_ctrl = '';

    /**
     * Dispatcher constructor.
     * @param Container $container
     */
    public function __construct (Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set default controller
     *
     * @param $controller_name
     */
    public function setDefaultController ($controller_name)
    {

        $this->default_ctrl = $controller_name;
    }

    /**
     * @param Request $request
     * @return mixed|ResponseInterface
     */
    public function dispatch (Request $request)
    {
        $controller = $this->container->factory->controller($request, $this->default_ctrl);

        if (!$controller) {
            throw new Exception\ControllerNotFound();
        }

        $crudTask = $this->getCrudTask($controller, $request->getHttpVerb(), $request->getVar(Controller::PARAM_ID, false));
        $task = $request->getCmd('task', $crudTask);

        /** @var BeforeExecute $event */
        $event = $controller->createAndTriggerEvent('Controller\BeforeExecute', [$controller, $task]);
        $this->triggerEvent($event);

        // Task was maybe overridden by event listeners?
        $task = $event->getTask();

        $response = $this->container->execute([$controller, $task]);

        /** @var AfterExecute $event */
        $event = $controller->createAndTriggerEvent('Controller\AfterExecute', [$controller, $task, $response]);
        $this->triggerEvent($event);

        // Response was maybe overridden by event listeners?
        $response = $event->getResponse();

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $content = $response;
        $view = $this->container->factory->view($request, $this->default_ctrl);

        $response = $this->container->factory->response($request);
        $response->setContent($view->display($content));

        return $response;
    }

    /**
     * Determines the CRUD task to use based on the view name and HTTP verb used in the request.
     * @credits https://github.com/akeeba/fof/blob/development/fof/Controller/DataController.php
     *
     * @return  string  The CRUD task (browse, read, edit, delete)
     */
    public function getCrudTask (Controller $controller, $httpVerb, $id = false)
    {
        $task = $controller->getDefaultTask();

        // Alter the task based on the verb
        switch ($httpVerb) {
            case 'POST':
            case 'PUT':
                $task = Controller::TASK_SAVE;
                break;
            case 'DELETE':
                if ($id) {
                    $task = Controller::TASK_DELETE;
                }
                break;
            case 'GET':
            default:
                if ($id) {
                    $task = Controller::TASK_READ;
                }
                break;
        }

        return $task;
    }
}
