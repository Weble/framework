<?php

namespace Zoolanders\Framework\Event\Type;

use Zoolanders\Framework\Event\HasSubjectInterface;

class Type extends \Zoolanders\Framework\Event\Event implements HasSubjectInterface
{
    /**
     * @var \Type
     */
    protected $type;

    /**
     * Beforesave constructor.
     * @param \Type $type
     */
    public function __construct (\Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return \Type
     */
    public function getType ()
    {
        return $this->type;
    }
}
