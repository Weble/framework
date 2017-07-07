<?php

namespace Zoolanders\Framework\Listener\Controller;

use Zoolanders\Framework\Event\Controller\AfterExecute;
use Zoolanders\Framework\Listener\Listener;

class TriggerOnAfterTaskMethods extends Listener
{
    /**
     * @param AfterExecute $event
     */
    public function handle (AfterExecute $event)
    {
        $task = $event->getTask();
        $controller = $event->getController();

        $method = 'onAfter' . ucfirst($task);

        if (method_exists($controller, $method)) {
            $controller->$method($event);
        }
    }
}
