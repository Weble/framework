<?php

namespace Zoolanders\Framework\Event\Element;

use Zoolanders\Framework\Event\Item\Item;

class Beforedisplay extends Item
{

    /**
     * @var bool
     */
    protected $render = true;

    /**
     * @var \Element|null
     */
    protected $element;

    /**
     * @var array
     */
    protected $params;

    /**
     * Beforedisplay constructor.
     * @param \Item $item
     * @param bool $render
     * @param \Element|null $element
     * @param array $params
     */
    public function __construct(\Item $item, &$render = true, \Element $element = null, $params = [])
    {
        parent::__construct($item);

        $this->render = &$render;
        $this->element = $element;
        $this->params = $params;
    }

    public function getRender()
    {
        return $this->render;
    }

    public function setRender($render)
    {
        $this->render = $render;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function getParams()
    {
        return $this->params;
    }
}
