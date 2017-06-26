<?php

namespace Zoolanders\Framework\Migration;

class Config extends \Phinx\Config\Config
{
    /**
     * {@inheritdoc}
     */
    public function __construct (array $configArray = [], $configFilePath = null)
    {
        $type = \JFactory::getConfig()->get('dbtype');

        if ($type == 'mysqli') {
            $type = 'mysql';
        }

        // On some system, PDO fails with localhost
        $host = \JFactory::getConfig()->get('host');
        $port = 3306;

        if (stripos($host, ":") !== false) {
            $tmp = explode(":", $host);
            $host = $tmp[0];
            $port = $tmp[1];
        }

        if (trim(strtolower($host)) == 'localhost') {
            $host = '127.0.0.1';
        }

        // Force the configuration using the joomla data and some standard zl stuff
        $defaultConfigArray = [
            'environments' => [
                'default_migration_table' => \JFactory::getConfig()->get('dbprefix') . 'zoo_zl_migrations',
                'production' => [
                    'name' => \JFactory::getConfig()->get('db'),
                    'user' => \JFactory::getConfig()->get('user'),
                    'pass' => \JFactory::getConfig()->get('password'),
                    'host' => $host,
                    'port' => $port,
                    'adapter' => $type,
                    'table_prefix' => \JFactory::getConfig()->get('dbprefix') . 'zoo_zl_'
                ]
            ],
            'paths' => [
                'migrations' => JPATH_LIBRARIES . '/zoolanders/installation/migrations',
                'seeds' => JPATH_LIBRARIES . '/zoolanders/installation/seeds'
            ]
        ];

        $configArray = array_merge($defaultConfigArray, $configArray);

        parent::__construct($configArray, $configFilePath);
    }
}
