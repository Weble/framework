<?php

namespace Zoolanders\Framework\Event\View;

use Zoolanders\Framework\Event\Event;
use Zoolanders\Framework\View\ViewInterface;

class View extends Event
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct (ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * @return ViewInterface
     */
    public function getView ()
    {
        return $this->view;
    }
}
