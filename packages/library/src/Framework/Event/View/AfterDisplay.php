<?php

namespace Zoolanders\Framework\Event\View;

use Zoolanders\Framework\View\ViewInterface;

class AfterDisplay extends View
{
    /**
     * @var string
     */
    protected $templateResult;

    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct (ViewInterface $view, &$templateResult)
    {
        parent::__construct($view);

        $this->templateResult = &$templateResult;
    }

    /**
     * @return mixed
     */
    public function &getTemplateResult ()
    {
        return $this->templateResult;
    }
}
