<?php

namespace Zoolanders\Framework\Service\Assets;

use Assetic\Filter\JSMinFilter;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Filesystem;
use Zoolanders\Framework\Service\Path;
use Zoolanders\Framework\Service\System\Document;

class Js extends Assets
{
    protected $filters = ['jsmin'];

    public function __construct(Document $document, Path $path, Filesystem $fs)
    {
        parent::__construct($document, $path, $fs);

        $this->filterManager->set('jsmin', new JSMinFilter());
    }

    protected function loadFile($path)
    {
        $this->document->addScript($path);
    }
}
