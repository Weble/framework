<?php

namespace Zoolanders\Framework\Event\Element;

class Afteredit extends Element
{
    /**
     * @var array
     */
    protected $html = [];

    /**
     * @var string
     */
    protected $elementName;

    /**
     * @var string
     */
    protected $elementDescription;

    /**
     * Afteredit constructor.
     * @param \Element $element
     * @param array $html
     */
    public function __construct(\Element $element, &$html = [], $description = '', $name = '')
    {
        parent::__construct($element);

        $this->html = &$html;
        $this->elementName = $name;
        $this->elementDescription = $description;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getElementDescription()
    {
        return $this->elementDescription;
    }

    public function getElementName()
    {
        return $this->elementName;
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }
}
