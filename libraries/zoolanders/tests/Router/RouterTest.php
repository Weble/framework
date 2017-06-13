<?php

namespace ZFTests\Router;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\TestRouter;

/**
 * Class RouterTest
 * Router testing class
 *
 * @package ZFTests\Router
 */
class RouterTest extends ZFTestCase
{
    /**
     * Test router init
     */
    public function testRouterInit(){
        $container = self::$container;
        $container->params->set('cache_routes', true);
        $router = new TestRouter($container);

        // Check if routes cache file created:
        $cache_file = $container->path->path('cache:') . '/routes';
        $this->assertFileExists($cache_file);
        /* Uncomment if PHPUnit version supports it: */
        //$this->assertFileIsReadable($container->path->path('cache:') . '/routes');
        unlink($cache_file);
        $this->assertFileNotExists($cache_file);
    }
}
