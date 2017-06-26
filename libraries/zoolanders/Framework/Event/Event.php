<?php

namespace Zoolanders\Framework\Event;

use Zoolanders\Framework\Container\Container;

/**
 * Class Event
 * @package Zoolanders\Event
 */
abstract class Event implements EventInterface
{
    /**
     * @var
     */
    protected $value;

    /**
     * Get the name of the event
     *
     * @return string
     */
    public function getName ()
    {
        $class = get_class($this);
        $parts = explode("\\", $class);

        if (count($parts) < 2) {
            return array_pop($parts);
        }

        $name = array_pop($parts);
        $name = array_pop($parts) . ':' . $name;

        return strtolower($name);
    }

    /**
     * Get the event properties set with the constructor
     * It assumes that  if you do new Event($config), you will set it to $this->config
     */
    public function getProperties ()
    {
        $self = new \ReflectionClass($this);
        $constructor = $self->getConstructor();

        $params = $constructor->getParameters();

        $properties = [];
        foreach ($params as $param) {
            $name = $param->getName();

            if (isset($this->$name)) {
                $properties[$name] = $this->$name;
            }
        }

        return $properties;
    }

    public function setReturnValue ($value)
    {
        $this->value = $value;
    }

    public function getReturnValue ()
    {
        return $this->value;
    }

}
