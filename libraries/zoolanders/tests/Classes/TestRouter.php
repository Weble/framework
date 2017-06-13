<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Router\Router;

class TestRouter extends Router
{

    /**
     * Route building
     */
    public function buildRoute(&$query, &$segments)
    {
        // Empty
    }

    /**
     * Route parsing
     */
    public function parseRoute(&$segments, &$vars)
    {
        // Empty
    }
}
