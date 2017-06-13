<?php

namespace Zoolanders\Framework\Table;

/**
 * Overrides zoo's table helper to get zoolanders table classes if needed
 * @package Zoolanders\Framework\Table
 */
class Helper extends \TableHelper
{
    /**
     * Class constructor
     *
     * @param \App $app A reference to an App Object
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_name = 'table';
    }

    /**
     * Get a table object
     *
     * @param string $name The name of the table to retrieve
     * @param string $prefix An alternative prefix
     *
     * @return \AppTable The table object
     *
     * @since 1.0.0
     */
    public function get($name, $prefix = null)
    {
        // Preload the zoo table class, probably we extend it
        $zooTable = parent::get($name, $prefix);

        $className = '\\Zoolanders\\Framework\\Table\\' . ucfirst(strtolower($name));

        if (class_exists($className)) {
            $this->_tables[$name] = new $className($this->app, $prefix . $name);
            return $this->_tables[$name];
        }

        return $zooTable;
    }
}
