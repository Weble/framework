<?php

namespace Zoolanders\Framework\Dispatcher;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Event\Dispatcher\AfterDispatch;
use Zoolanders\Framework\Event\Dispatcher\BeforeDispatch;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\Service\Environment;
use Zoolanders\Framework\Service\Event;

class Dispatcher {
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
     */
    public function __construct (Container $container) {
        $this->container = $container;
    }

    /**
     * Set default controller
     *
     * @param $controller_name
     */
    public function setDefaultController ($controller_name) {

        $this->default_ctrl = $controller_name;
    }

    /**
     * @return ResponseInterface
     */
    public function dispatch (Request $request) {
        $controller = $this->container->factory->controller($request, $this->default_ctrl);

        if (!$controller) {
            throw new Exception\ControllerNotFound();
        }

        $response = $this->container->execute([$controller, $request->getCmd('task', $controller->getDefaultTask())]);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $content = $response;
        $view = $this->container->factory->view($request, $this->default_ctrl);

        $response = $this->container->factory->response($request);
        $response->setContent($view->render($content));

        return $response;
    }
}
