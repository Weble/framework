<?php

define('VENDOR_DIR', __DIR__ . "/../vendor/");
define('FIXTURES_PATH', __DIR__ . '/fixtures');
define('FRAMEWORK_ROOT', __DIR__ . '/../Framework');
define('ZL_TEST', true);


// Bootstrap Joomla env:

// Path to prepared joomla environment:
define('JOOMLA_ENV_PATH', VENDOR_DIR . 'zoolanders/framework-test-env');

if (!defined('JPATH_TESTS')) {
    define('JPATH_TESTS', __DIR__);
}

require_once JOOMLA_ENV_PATH . '/joomla-env-bootstrap.php';

// Bootstrap Framework Classes:
$loader = require_once(dirname(dirname(__FILE__)) . '/include.php');

$loader->addPsr4('ZFTests\\', dirname(__FILE__));

// Register the core Joomla test classes.
// JLoader::registerPrefix('Test', __DIR__ . '/core');