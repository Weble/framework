<?php

namespace Zoolanders\Framework\Event;

interface EventInterface
{
    public function getName ();

    public function getProperties ();

    public function setReturnValue ($value);

    public function getReturnValue ();
}
