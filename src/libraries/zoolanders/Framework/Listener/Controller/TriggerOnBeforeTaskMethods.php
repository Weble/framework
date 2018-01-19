<?php

namespace Zoolanders\Framework\Listener\Controller;

use Zoolanders\Framework\Event\Controller\BeforeExecute;
use Zoolanders\Framework\Listener\Listener;

class TriggerOnBeforeTaskMethods extends Listener
{
    /**
     * @param BeforeExecute $event
     */
    public function handle (BeforeExecute $event)
    {
        $task = $event->getTask();
        $controller = $event->getController();

        $method = 'onBefore' . ucfirst($task);

        if (method_exists($controller, $method)) {
            $controller->$method($event);
        }
    }
}
