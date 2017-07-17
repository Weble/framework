<?php

if (!defined('ZL_TEST')) {
    define('ZL_TEST', false);
}

require_once dirname(__FILE__) . '/Framework/Autoloader/Autoloader.php';
$composerAutoloader = require_once dirname(__FILE__) . '/vendor/autoload.php';

// Create autoloader and add the mapping to the framework
$loader = \Zoolanders\Framework\Autoloader\Autoloader::getInstance($composerAutoloader);
$loader->addPsr4('Zoolanders\\Framework\\', dirname(__FILE__) . '/Framework');

return $loader;
