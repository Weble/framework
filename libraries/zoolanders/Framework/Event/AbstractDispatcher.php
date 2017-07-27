<?php

namespace Zoolanders\Framework\Event;

use Zoolanders\Framework\Container\Container;

abstract class AbstractDispatcher implements DispatcherInterface
{
    /**
     * The listeners for the events
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Connects a listener to a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     */
    public function connect ($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }

        $this->listeners[$name][] = $listener;
    }

    /**
     * Disconnects a listener for a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     *
     * @return mixed false if listener does not exist, null otherwise
     */
    public function disconnect ($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            return false;
        }

        foreach ($this->listeners[$name] as $i => $callable) {
            if ($listener === $callable) {
                unset($this->listeners[$name][$i]);
            }
        }
    }

    /**
     * @param $string
     * @param array $args
     * @param null $namespace
     * @return mixed
     */
    public function create ($string, $args = [], $namespace = null)
    {
        $container = Container::getInstance();

        // Prefix generic namespace?
        if (!$namespace) {
            $namespace = Container::FRAMEWORK_NAMESPACE . 'Event\\';
        }

        return $container->make($namespace . $string, $args);
    }


    /**
     * @see notify
     */
    public function trigger (EventInterface &$event)
    {
        $this->notify($event);
    }

    /**
     * @param $name
     * @param array $args
     * @param null $namespace
     * @return mixed
     */
    public function createAndTrigger($name, $args = [], $namespace = null)
    {
        $event = $this->create($name, $args, $namespace);
        $this->trigger($event);

        return $event;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function notify (EventInterface &$event)
    {
        $container = Container::getInstance();

        // @TODO: Add processing for non-encapsulated listeners (like closures, global func. etc.)
        foreach ($this->getListeners($event->getName()) as $listener) {
            $parts = explode("@", $listener);

            $method = 'handle';
            $callback = $listener;

            // we have a function to call
            if (count($parts) >= 2) {
                $listener = $parts[0];
                $method = $parts[1];
            }

            if (class_exists($listener)) {
                $listenerClass = $container->make($listener);
                $callback = [$listenerClass, $method];
            }

            $container->execute($callback, [&$event]);
        }
    }

    /**
     * Returns true if the given event name has some listeners.
     *
     * @param string $name The event name
     *
     * @return boolean true if some listeners are connected, false otherwise
     */
    public function hasListeners ($name)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }

        // Both local and zoo's
        return (boolean)count($this->listeners[$name]);
    }

    /**
     * Returns all listeners associated with a given event name.
     *
     * @param string $name The event name
     *
     * @return array An array of listeners
     */
    public function getListeners ($name)
    {
        if (!isset($this->listeners[$name])) {
            return array();
        }

        // merge ours with zoo's
        return $this->listeners[$name];
    }

    /**
     * @param $events
     */
    public function bindEvents ($events)
    {
        foreach ($events as $event => $listeners) {
            $listeners = (array)$listeners;

            foreach ($listeners as $listener) {
                $this->connect($event, $listener);
            }
        }
    }
}