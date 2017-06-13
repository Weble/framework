<?php

namespace Zoolanders\Framework\Event\Element;

class Configform extends Element
{
    /**
     * @var \JForm
     */
    protected $form;

    /**
     * Afteredit constructor.
     * @param \Element $element
     * @param  $form
     */
    public function __construct(\Element $element, $form = null)
    {
        parent::__construct($element);

        $this->form = $form;
    }

    /**
     * @return \JForm|null
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }
}
