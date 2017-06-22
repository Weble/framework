<?php

namespace Zoolanders\Framework;

use Composer\Autoload\ClassLoader;

// Register utility functions
require_once __DIR__ . '/Framework/Utils/ArrayUtils.php';

class Autoloader {
    /**
     * @var ClassLoader
     */
    private static $loader;

    private static $composerAutoloader;

    /**
     * Autoloader constructor. Private!!! Just use getInstance
     * @param ClassLoader $composerAutoloader
     */
    private function __construct (ClassLoader $composerAutoloader = null) {
        if ($composerAutoloader) {
            self::$composerAutoloader = $composerAutoloader;
        }
    }

    /**
     * Proxy calls to the loader of Composer
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call ($name, $arguments = []) {
        return call_user_func_array([self::$composerAutoloader, $name], $arguments);
    }

    /**
     * Singleton pattern
     * @param ClassLoader $composerAutoloader
     * @return Autoloader
     */
    public static function &getInstance (ClassLoader $composerAutoloader = null) {
        if (self::$loader !== null) {
            return self::$loader;
        }

        self::$loader = new Autoloader($composerAutoloader);

        return self::$loader;
    }
}

if (!defined('ZF_TEST')) {
    define('ZF_TEST', false);
}

$composerAutoloader = require_once dirname(__FILE__) . '/vendor/autoload.php';

// Create autoloader and add the mapping to the framework
$loader = Autoloader::getInstance($composerAutoloader);
$loader->addPsr4('Zoolanders\\Framework\\', dirname(__FILE__) . '/Framework');

return $loader;
