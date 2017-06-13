<?php

namespace Zoolanders\Framework\Repository;

use Zoolanders\Framework\Container\Container;

abstract class Repository implements RepositoryInterface
{
    public function __get($name)
    {
        if ($name == 'container') {
            return Container::getInstance();
        }
    }
}
