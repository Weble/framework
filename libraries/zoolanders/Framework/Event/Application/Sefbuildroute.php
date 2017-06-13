<?php

namespace Zoolanders\Framework\Event\Application;

use Zoolanders\Framework\Event\Event;

class Sefbuildroute extends Event
{
    protected $path = [];

    public function __construct($segments, $query)
    {
        $this->path = ['segments' => $segments, 'query' => $query];
    }

    public function getPath()
    {
        return $this->path;
    }
}
