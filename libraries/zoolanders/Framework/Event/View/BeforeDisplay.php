<?php

namespace Zoolanders\Framework\Event\View;

class BeforeDisplay extends View {
    protected $tpl;

    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct (\Zoolanders\Framework\View\View $view, $tpl) {
        $this->view = $view;
        $this->tpl = $tpl;
    }

    /**
     * @return mixed
     */
    public function getTpl () {
        return $this->tpl;
    }

    /**
     * @return View
     */
    public function getView () {
        return $this->view;
    }
}
