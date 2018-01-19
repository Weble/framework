<?php

namespace Zoolanders\Framework\Event;

use Zoolanders\Framework\Service\Zoo;

class Dispatcher extends AbstractDispatcher
{
    /**
     * @var \JEventDispatcher
     */
    public $joomla;

    /**
     * @var \Zoolanders\Framework\Event\Zoo
     */
    public $zoo;

    /***
     * Dispatcher constructor.
     * @param Zoo $zooService
     */
    public function __construct (Zoo $zooService)
    {
        $this->zoo = new \Zoolanders\Framework\Event\Zoo($this, $zooService);
        $this->joomla = \JEventDispatcher::getInstance();

        // Load every zoolanders plugin by default
        \JPluginHelper::importPlugin('zoolanders');
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
        parent::disconnect($name, $listener);

        // also, disconnect it to the core zoo listeners to keep b/c
        $this->zoo->zoo->dispatcher->disconnect($name, $listener);
    }

    /**
     * @param EventInterface $event
     */
    public function notify (EventInterface &$event)
    {
        $eventName = 'onZoolanders' . $event->getClassName();

        // First, trigger the joomla event
        $this->joomla->trigger($eventName, [&$event]);

        parent::notify($event);
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
}
