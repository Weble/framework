<?php

namespace Zoolanders\Framework\Listener;

interface ListenerInterface
{
    public function handle (\Zoolanders\Framework\Event\EventInterface $event);
}
