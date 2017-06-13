<?php

namespace Zoolanders\Framework\Service\Assets;

use Assetic\AssetManager;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\FilterManager;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Filesystem;
use Zoolanders\Framework\Service\Path;
use Zoolanders\Framework\Service\Service;
use Zoolanders\Framework\Service\System\Document;

abstract class Assets
{
    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var
     */
    protected $assets = [];

    /**
     * Assets constructor.
     * @param Container $c
     */
    public function __construct(Document $document, Path $path, Filesystem $fs)
    {
        $this->assetManager = new AssetManager();
        $this->filterManager = new FilterManager();
        $this->factory = new AssetFactory(JPATH_SITE);

        $this->factory->setAssetManager($this->assetManager);
        $this->factory->setFilterManager($this->filterManager);
        $this->factory->addWorker(new CacheBustingWorker());

        $this->document = $document;
        $this->path = $path;
        $this->filesystem = $fs;
    }

    public function define($name, $assets)
    {
        settype($assets, 'array');
        $this->assetManager->set($name, $this->factory->createAsset($assets));
    }

    public function add($assets)
    {
        settype($assets, 'array');

        foreach ($assets as &$asset) {
            $asset = $this->path->path($asset);
        }

        $this->assets = array_unique(array_merge($this->assets, $assets));
    }

    public function load($filters = false)
    {
        if (!$filters) {
            $filters = $this->filters;
        }

        $asset = $this->factory->createAsset($this->assets, $filters);

        $writer = new Writer($this->filesystem, $this->path->path('cache:'));
        $writer->writeAsset($asset);

        foreach ($writer->getPaths() as $path) {
            $this->loadFile($path);
        }
    }

    abstract protected function loadFile($path);
}
