<?php

namespace Zoolanders\Framework\Event\Element;

class Element extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Element
     */
    protected $element;

    /**
     * Element constructor.
     * @param \Element $element
     */
    public function __construct(\Element $element)
    {
        $this->element = $element;
    }

    /**
     * @return \Element
     */
    public function getElement()
    {
        return $this->element;
    }
}
