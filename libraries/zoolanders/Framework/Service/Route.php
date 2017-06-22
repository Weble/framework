<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Container\Container;

class Route {
    public function __construct (Zoo $zoo) {
        $this->zoo = $zoo;
        $this->app = $zoo->getApp();
        $this->setupRouters();
    }

    /**
     *  Setup the custom routers for each application
     */
    protected function setupRouters () {
        $this->zoo->getApp()->event->dispatcher->connect('application:sefparseroute', function ($event) {

            $app_id = $this->app->request->getInt('app_id', null);
            $app = $this->app->table->application->get($app_id);

            // check if was loaded
            if (!$app) return;

            $group = $app->getGroup();
            if ($router = $this->app->path->path("applications:$group/router.php")) {

                require_once $router;

                $class = 'ZLRouter' . ucfirst($group);
                $routerClass = new $class;
                $routerClass->parseRoute($event);
            }
        });
    }
}
