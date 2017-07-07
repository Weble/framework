<?php

namespace Zoolanders\Framework\Controller;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Controller\AfterExecute;
use Zoolanders\Framework\Event\Controller\BeforeExecute;
use Zoolanders\Framework\Event\Exception\EventNotFound;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Utils\NameFromClass;

/**
 * Basic Controller Class
 */
class Controller implements ControllerInterface
{
    use Triggerable, NameFromClass;

    /**
     * defaultTask
     *
     * @var    string
     */
    protected $defaultTask = ControllerInterface::TASK_INDEX;

    /**
     * The current view name; you can override it in the configuration
     *
     * @var string
     */
    protected $view = '';

    /**
     * The current layout; you can override it in the configuration
     *
     * @var string
     */
    protected $layout = 'default';

    /**
     * Controller constructor.
     */
    public function __construct ()
    {
        $this->registerDefaultTask($this->defaultTask ? $this->defaultTask : 'index');
    }

    /**
     * @return string
     */
    public function getDefaultTask ()
    {
        return $this->defaultTask;
    }

    /**
     * Register the default task to perform if a mapping is not found.
     *
     * @param   string $method The name of the method in the derived class to perform if a named task is not found.
     *
     * @return  ControllerInterface  This object to support chaining.
     */
    public function registerDefaultTask ($method)
    {
        $this->defaultTask = $method;

        return $this;
    }

    /**
     * @param BeforeExecute $event
     */
    protected function onBeforeExecute(BeforeExecute $event)
    {
        // Event will be \Extension\Namespace\ControllerName\BeforeTask (\Zoolanders\Zooadmin\Extensions\BeforeSave)
        $eventName = $this->getName() . '\\Before' . ucfirst($event->getTask());

        try {
            $this->createAndTriggerEvent($this->getControllerNameSpace() . $eventName, [], 'onBefore' . ucfirst($event->getTask()));
        } catch (EventNotFound $e) {
            // Ignore, event doesn't exist
        }
    }

    /**
     * @param AfterExecute $event
     */
    protected function onAfterExecute(AfterExecute $event)
    {
        // Event will be \Extension\Namespace\ControllerName\AfterTask (\Zoolanders\Zooadmin\Extensions\AfterIndex)
        $eventName = __NAMESPACE__ . '\\' . $this->getName() . '\\After' . ucfirst($event->getTask());

        try {
            $controllerEvent = $this->createAndTriggerEvent($eventName, [$event->getResponse()], $this->getControllerNameSpace());

            if ($controllerEvent instanceof \Zoolanders\Framework\Event\Controller\Controller) {
                $event->setResponse($controllerEvent->getResponse());
            }

        } catch (EventNotFound $e) {
            // Ignore, event doesn't exist
        }
    }

    /**
     * @return string
     */
    protected function getControllerNameSpace()
    {
        $namespace = explode("\\", get_class($this));
        array_pop($namespace);
        array_pop($namespace);

        return '\\' . implode("\\", $namespace) . '\\';
    }
}
