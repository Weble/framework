<?php

namespace Zoolanders\Framework\Event\Type;

class Type extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Type
     */
    protected $type;

    /**
     * Beforesave constructor.
     * @param \Type $type
     */
    public function __construct(\Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return \Type
     */
    public function getType()
    {
        return $this->type;
    }
}
