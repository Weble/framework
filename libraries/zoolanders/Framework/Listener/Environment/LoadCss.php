<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\System\Document;

class LoadCss extends Listener {
    function __construct (Document $document) {
        $this->document = $document;
    }

    /**
     * @param \Zoolanders\Framework\Event\Environment\Init $event
     */
    public function handle (\Zoolanders\Framework\Event\Environment\Init $event) {
    }
}
