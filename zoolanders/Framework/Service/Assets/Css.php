<?php

namespace Zoolanders\Framework\Service\Assets;

use Assetic\Filter\CssMinFilter;
use Assetic\Filter\LessphpFilter;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Filesystem;
use Zoolanders\Framework\Service\Path;
use Zoolanders\Framework\Service\System\Document;

class Css extends Assets
{
    protected $filters = ['less', 'cssmin'];

    public function __construct(Document $document, Path $path, Filesystem $fs)
    {
        parent::__construct($document, $path, $fs);

        $this->filterManager->set('cssmin', new CssMinFilter());
        $this->filterManager->set('less', new LessphpFilter());
    }

    protected function loadFile($path)
    {
        $this->document->addStylesheet($path);
    }
}
