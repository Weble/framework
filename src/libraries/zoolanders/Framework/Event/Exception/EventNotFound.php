<?php

namespace Zoolanders\Framework\Event\Exception;

use Auryn\InjectorException;

class EventNotFound extends \Exception
{
    /**
     * EventNotFound constructor.
     * @param InjectorException $e
     * @param int $eventName
     * @param array $args
     */
    public function __construct (InjectorException $e, $eventName, $args = [])
    {
        $message = 'Event ' . $eventName . ' not found (args: ' . json_encode($args) . ')';

        parent::__construct($message, $e->getCode(), $e);
    }
}