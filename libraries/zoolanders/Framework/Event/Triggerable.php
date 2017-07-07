<?php

namespace Zoolanders\Framework\Event;

use Auryn\InjectorException;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Exception\EventNotFound;

trait Triggerable
{
    /**
     * @param EventInterface $event
     */
    public function triggerEvent (EventInterface $event)
    {
        $container = Container::getInstance();
        $eventName = 'on' . $event->getClassName();

        // let's try locally
        if (method_exists($this, $eventName)) {
            $this->$eventName($event);
        }

        // First, trigger the joomla event
        $container->event->joomla->trigger($eventName, [&$event]);

        // Then trigger also the zoolanders one
        $container->event->trigger($event);
    }

    /**
     * @param $eventName
     * @param array $args
     * @param null $namespace
     * @return EventInterface
     */
    public function createAndTriggerEvent($eventName, $args = [], $namespace = null)
    {
        $event = $this->createEvent($eventName, $args, $namespace);
        $this->triggerEvent($event);

        return $event;
    }

    /**
     * @param $eventName
     * @param array $args
     * @param null $namespace
     * @return mixed
     * @throws EventNotFound
     */
    public function createEvent($eventName, $args = [], $namespace = null)
    {
        $container = Container::getInstance();

        try {
            return $container->event->create($eventName, $args, $namespace);
        } catch (InjectorException $e) {
            throw new EventNotFound($e, $eventName, $args);
        }
    }
}
