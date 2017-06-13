<?php

namespace Zoolanders\Framework\Service\Assets;

use Assetic\Asset\AssetInterface;
use Assetic\AssetWriter;
use Assetic\Util\VarUtils;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Filesystem;

class Writer extends AssetWriter
{
    protected $dir;

    protected $values;

    protected $paths = [];

    /**
     * Writer constructor.
     * @param Container $container
     * @param array $dir
     * @param array $values
     */
    public function __construct(Filesystem $fs, $dir, $values = [])
    {
        parent::__construct($dir);

        $this->filesystem = $fs;
        $this->values = $values;
        $this->dir = $dir;
    }

    public function writeAsset(AssetInterface $asset)
    {
        foreach (VarUtils::getCombinations($asset->getVars(), $this->values) as $combination) {
            $asset->setValues($combination);

            $path = $this->dir . '/' . VarUtils::resolve(
                    $asset->getTargetPath(),
                    $asset->getVars(),
                    $asset->getValues()
                );

            $this->writeAssetFile(
                $path,
                $asset->dump()
            );

            $this->paths[] = $path;
        }
    }

    public function getPaths()
    {
        return array_unique($this->paths);
    }

    protected function writeAssetFile($path, $contents)
    {
        if (!$this->filesystem->has($path)) {
            $this->filesystem->write($path, $contents);
        }
    }
}
