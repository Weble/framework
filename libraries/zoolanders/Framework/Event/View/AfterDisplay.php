<?php

namespace Zoolanders\Framework\Event\View;

class AfterDisplay extends View
{
    protected $tpl;

    protected $templateResult;

    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct(\Zoolanders\Framework\View\View $view, $tpl, &$templateResult)
    {
        $this->view = $view;
        $this->tpl = $tpl;
        $this->templateResult = &$templateResult;
    }

    /**
     * @return mixed
     */
    public function getTpl()
    {
        return $this->tpl;
    }

    /**
     * @return mixed
     */
    public function &getTemplateResult()
    {
        return $this->templateResult;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }
}
