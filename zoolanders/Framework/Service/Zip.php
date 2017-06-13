<?php

namespace Zoolanders\Framework\Service;

use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class Zip
{
    function __construct(Filesystem $fs)
    {
        $this->filesystem = $fs;
    }

    /**
     * @param $file
     * @return Filesystem
     */
    public function open($file)
    {
        return new Filesystem(new ZipArchiveAdapter($file));
    }

    public function create($file, $files = [])
    {
        $zip = $this->open($file);

        settype($files, 'array');

        foreach ($files as $file) {
            if ($this->filesystem->has($file)) {
                $zip->write(basename($file), $this->filesystem->read($file));
            }
        }

        $zip->getAdapter()->getArchive()->close();
    }
}
