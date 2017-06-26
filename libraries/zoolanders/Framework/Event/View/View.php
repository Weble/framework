<?php

namespace Zoolanders\Framework\Event\View;

use Zoolanders\Framework\Event\Event;

class View extends Event
{
    protected $view;

    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct (\Zoolanders\Framework\View\View $view)
    {
        $this->view = $view;
    }

    /**
     * @return \Zoolanders\Framework\View\View
     */
    public function getView ()
    {
        return $this->view;
    }
}
