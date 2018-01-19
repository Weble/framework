<?php

namespace Zoolanders\Framework\Event\View;

use Zoolanders\Framework\View\ViewInterface;

class BeforeDisplay extends View
{
    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct (ViewInterface $view)
    {
        parent::__construct($view);
    }
}
