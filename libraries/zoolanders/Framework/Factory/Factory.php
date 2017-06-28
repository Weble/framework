<?php

namespace Zoolanders\Framework\Factory;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Request\Request;
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
        $type = $input->isAjax() ? 'Json' : 'Html';

        $responseClass = '\Zoolanders\Framework\Response\\' . $type . 'Response';

        return $this->container->make($responseClass);
    }

    /**
     * @param Request $input
     * @param null $default_ctrl
     * @return bool|Controller
     */
    public function controller (Request $input, $default_ctrl = null)
    {
        $namespaces = [];
        $namespaces[] = Container::FRAMEWORK_NAMESPACE;

        $controller = $input->getCmd('controller', $input->getCmd('view', $default_ctrl));

        if ($extension = $this->container->environment->currentExtension()) {
            $namespaces = array_merge($this->container->getRegisteredExtensionNamespaces($extension), $namespaces);
        }

        foreach ($namespaces as $namespace) {
            $class = $namespace . 'Controller\\' . ucfirst($controller);

            if (class_exists($class)) {
                // perform the request task
                /** @var Controller $ctrl */
                return $this->container->make($class);
            }
        }

        return false;
    }

    /**
     * Make response
     *
     * @param Request $input
     * @param null $default
     * @return ViewInterface
     */
    public function view (Request $input, $default = null)
    {
        $type = $input->isAjax() ? 'Json' : 'Html';
        $name = $input->getCmd('view', $input->getCmd('controller', $default));

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
