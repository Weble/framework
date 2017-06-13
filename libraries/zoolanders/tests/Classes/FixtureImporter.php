<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Container\Container;

/**
 * Class FixtureImporter
 * SQL fixture importer
 *
 * @package ZFTests\Classes
 */
class FixtureImporter
{
    /**
     * @var     DI
     */
    protected $container;

    /**
     * @var array   Fixture import config
     */
    protected $config = [];

    /**
     * FixtureImporter constructor.
     */
    public function __construct(Container $container, $config = []){
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * Import file by fixture pkg name
     *
     * @param $pkg_name
     * @throws \Exception
     */
    public function import($pkg_name){
        // Lookup for fixtures file:
        $path = realpath(FIXTURES_PATH . '/' . $this->config['path'] . '/' . $pkg_name . '.sql');

        if(file_exists($path)){
            $this->processSql($path);
        } else {
            // Silent exit
        }
    }

    /**
     * Parse sql content
     *
     * @param   string Full path to sql dump file
     */
    protected function processSql($resource){
        try{
            $db = $this->container->db;
            $res = fopen($resource, 'r');

            while( !feof($res) ){
                $line = fgets($res);

                $db->setQuery($line);
                $db->execute();

                if($msg = $db->getErrorMessage($line)){
                    throw new \Exception($msg);
                }
            }

            fclose($res);
        } catch(\Exception $e) {

        }
    }
}
