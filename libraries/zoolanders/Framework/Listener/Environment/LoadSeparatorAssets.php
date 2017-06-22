<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\System\Document;

class LoadSeparatorAssets extends Listener {
    /**
     * @var Document
     */
    protected $document;

    /**
     * LoadSeparatorAssets constructor.
     * @param Document $document
     */
    function __construct (Document $document) {
        $this->document = $document;
    }

    /**
     * @param \Zoolanders\Framework\Event\Environment\Init $event
     */
    public function handle (\Zoolanders\Framework\Event\Environment\Init $event) {
        // perform admin tasks
        if ($event->is('zoo-type')) {
            $this->document->addStylesheet('elements:separator/assets/zlfield.css');
            $this->document->addScript('elements:separator/assets/zlfield.min.js');
            $this->document->addScriptDeclaration('jQuery(function($) { $("body").ZOOtoolsSeparatorZLField({ enviroment: "' . $event->get() . '" }) });');
        }
    }
}
