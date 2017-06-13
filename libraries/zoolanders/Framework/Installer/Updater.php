<?php

namespace Zoolanders\Framework\Installer;

use Zoolanders\Framework\Container\Container;

abstract class Updater
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $obsolete = [];

    /**
     * Updater constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Check if column exists in specified table
     */
    function column_exists($column, $table)
    {
        $exists = false;
        $this->container->db->setQuery("SHOW columns FROM `{$table}`");
        $columns = $this->contaner->db->loadAssocList();

        if (is_array($columns)) while (list ($key, $val) = each($columns)) {
            if ($val['Field'] == $column) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    /**
     * Removes obsolete files and folders
     */
    public function removeObsolete()
    {
        // Remove files
        if (isset($this->obsolete['files']) && !empty($this->obsolete['files']))
            foreach ($this->obsolete['files'] as $file) {
                $f = JPATH_ROOT . '/' . $file;
                if (!$this->container->filesystem->has($f)) continue;
                $this->container->filesystem->delete($f);
            }

        // Remove folders
        if (isset($this->obsolete['folders']) && !empty($this->obsolete['folders']))
            foreach ($this->obsolete['folders'] as $folder) {
                $f = JPATH_ROOT . '/' . $folder;
                if (!$this->container->filesystem->has($f)) continue;
                $this->container->filesystem->delete($f);
            }
    }

    /**
     * Performs the update
     */
    abstract public function run();
}
