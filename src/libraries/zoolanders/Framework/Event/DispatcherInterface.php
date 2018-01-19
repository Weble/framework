<?php
namespace Zoolanders\Framework\Event;

interface DispatcherInterface
{
    /**
     * Connects a listener to a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     */
    public function connect ($name, $listener);

    /**
     * Disconnects a listener for a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     *
     * @return mixed false if listener does not exist, null otherwise
     */
    public function disconnect ($name, $listener);

    /**
     * @param $string
     * @param array $args
     * @param null $namespace
     * @return mixed
     */
    public function create ($string, $args = [], $namespace = null);

    /**
     * @see notify
     */
    public function trigger (EventInterface &$event);

    /**
     * @param $name
     * @param array $args
     * @param null $namespace
     * @return mixed
     */
    public function createAndTrigger ($name, $args = [], $namespace = null);

    /**
     * @param EventInterface $event
     * @return void
     */
    public function notify (EventInterface &$event);

    /**
     * Returns true if the given event name has some listeners.
     *
     * @param string $name The event name
     *
     * @return boolean true if some listeners are connected, false otherwise
     */
    public function hasListeners ($name);

    /**
     * Returns all listeners associated with a given event name.
     *
     * @param string $name The event name
     *
     * @return array An array of listeners
     */
    public function getListeners ($name);

    /**
     * @param $events
     */
    public function bindEvents ($events);
}