<?php

namespace Zoolanders\Framework\Factory;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\ControllerInterface;
use Zoolanders\Framework\Request\JsonRequest;
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Request\RequestInterface;
use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\View\ViewInterface;

/**
 * Class Factory
 * @package Zoolanders\Framework\Factory
 */
class Factory
{
    /**
     * Factory constructor.
     * @param Container $container
     */
    public function __construct (Container $container)
    {
        $this->container = $container;
    }

    /**
     * Make response
     * @param Request $input
     * @return ResponseInterface
     */
    public function response (Request $input)
    {
        $type = $input->getExpectedResponse();

        switch ($type) {
            case ResponseInterface::TYPE_JSON:
                $type = 'Json';
                break;
            case ResponseInterface::TYPE_HTML:
            default:
                $type = 'Html';
                break;
        }

        $responseClass = '\Zoolanders\Framework\Response\\' . $type . 'Response';

        return $this->container->make($responseClass);
    }

    /**
     * Make response
     * @param Request $input
     * @return RequestInterface
     */
    public function request (Request $input)
    {
        $type = '';

        if ($type == ResponseInterface::TYPE_JSON) {
            $requestClass = '\Zoolanders\Framework\Request\JsonRequest';
            return $this->container->make($requestClass);
        }

        return $input;
    }

    /**
     * Make response
     * @param Request $input
     * @return ResponseInterface
     */
    public function errorResponse (Request $input, \Exception $e = null)
    {
        $type = $input->getExpectedResponse();

        switch ($type) {
            case ResponseInterface::TYPE_JSON:
                $type = 'Json';
                break;
            case ResponseInterface::TYPE_HTML:
            default:
                $type = 'Html';
                break;
        }

        $responseClass = '\Zoolanders\Framework\Response\Error\\' . $type . 'Response';

        return $this->container->make($responseClass, [

        ]);
    }

    /**
     * @param Request $input
     * @param null $defaultController
     * @return null|ControllerInterface
     */
    public function controller (Request $input, $defaultController = null)
    {
        $namespaces = [];
        $namespaces[] = Container::FRAMEWORK_NAMESPACE;

        $controller = $input->getCmd('controller', $input->getCmd('view', $defaultController));

        if ($extension = $this->container->environment->currentExtension()) {
            $namespaces = array_merge($this->container->getRegisteredExtensionNamespaces($extension), $namespaces);
        }

        foreach ($namespaces as $namespace) {
            $class = $namespace . 'Controller\\' . ucfirst($controller);

            if (class_exists($class)) {
                // perform the request task
                /** @var ControllerInterface $ctrl */
                return $this->container->make($class);
            }
        }

        return null;
    }

    /**
     * Make response
     *
     * @param Request $input
     * @param null $defaultController
     * @return ViewInterface
     */
    public function view (Request $input, $defaultController = null)
    {
        $type = $input->getExpectedResponse();

        switch ($type) {
            case ResponseInterface::TYPE_JSON:
                $type = 'Json';
                break;
            case ResponseInterface::TYPE_HTML:
            default:
                $type = 'Html';
                break;
        }

        $name = $input->getCmd('view', $input->getCmd('controller', $defaultController));

        $component = $this->container->environment->currentExtension();
        $namespaces = $this->container->getRegisteredExtensionNamespaces($component);

        if (!empty($namespaces)) {
            // Lookup for view class among namespaces
            foreach ($namespaces as $namespace) {
                $viewClass = $namespace . 'View\\' . ucfirst($name) . '\\' . $type;
                if (class_exists($viewClass)) {
                    return $this->container->make($viewClass);
                }
            }
        }

        // Fallback to core view:
        return $this->container->make('\Zoolanders\Framework\View\\' . $type);
    }
}
