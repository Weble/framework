<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Zoo as ZooService;

class Dispatcher
{
    /**
     * @var Zoo
     */
    public $zoo;

    /**
     * @var \JEventDispatcher
     */
    public $joomla;

    /**
     * The listeners for the events
     *
     * @var array
     * @since 1.0.0
     */
    protected $listeners = array();

    /**
     * Event constructor.
     */
    public function __construct(ZooService $zoo)
    {
        $this->zoo = new Zoo($this, $zoo);
        $this->joomla = \JEventDispatcher::getInstance();
    }

    /**
     * Connects a listener to a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     *
     * @since 1.0.0
     */
    public function connect($name, $listener)
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
     *
     * @since 1.0.0
     */
    public function disconnect($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            return false;
        }

        foreach ($this->listeners[$name] as $i => $callable) {
            if ($listener === $callable) {
                unset($this->listeners[$name][$i]);
            }
        }

        // also, disconnect it to the core zoo listeners to keep b/c
        $this->zoo->zoo->dispatcher->disconnect($name, $listener);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function create($string)
    {
        $container = Container::getInstance();
        return $container->make(Container::FRAMEWORK_NAMESPACE . 'Event\\' . $string);
    }


    /**
     * @see notify
     */
    public function trigger(EventInterface &$event)
    {
        /*if(ZF_TEST){
            // Test mode, notify event catcher service
            $this->container->eventstack->push($event->getName(), $event);
        }*/

        return $this->notify($event);
    }

    /**
     * @param EventInterface $event
     * @return bool|void
     */
    public function notify(EventInterface &$event)
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

            return $container->execute($callback, [&$event]);
        }

        return false;
    }

    /**
     * Returns true if the given event name has some listeners.
     *
     * @param string $name The event name
     *
     * @return boolean true if some listeners are connected, false otherwise
     *
     * @since 1.0.0
     */
    public function hasListeners($name)
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
     *
     * @since 1.0.0
     */
    public function getListeners($name)
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
    public function bindEvents($events)
    {
        foreach ($events as $event => $listeners) {
            $listeners = (array)$listeners;

            foreach ($listeners as $listener) {
                $this->connect($event, $listener);
            }
        }
    }
}
