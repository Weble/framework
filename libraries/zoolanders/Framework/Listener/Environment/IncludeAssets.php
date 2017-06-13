<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\Assets\Css;
use Zoolanders\Framework\Service\Assets\Js;

class IncludeAssets extends Listener
{
    public function __construct(Css $css, Js $js)
    {
        $this->css = $css;
        $this->js = $js;
    }

    /**
     * @param \Zoolanders\Framework\Event\Environment\BeforeRender $event
     */
    public function handle(\Zoolanders\Framework\Event\Environment\BeforeRender $event)
    {
        $this->css->load();
        $this->js->load();
    }
}
