<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Collection\Collection;
use Zoolanders\Framework\Service\Alerts\Error;

/**
 * Class Alerts
 * Alerts manager service
 *
 * @package Zoolanders\Framework\Service
 */
class Alerts
{
    /**
     * @var Notifications pool
     */
    protected $notifications;

    /**
     * Alerts constructor.
     */
    public function __construct()
    {
        $this->notifications = new Collection();
    }

    /**
     * Add error to scope collection
     *
     * @param   mixed
     * @param   string  Scope
     */
    public function push($error, $scope = 'default')
    {
        if(!($error instanceof Error)){
            $error = new Error($error);
        }
        if(!$this->notifications->has($scope)){
            $this->notifications->put($scope, new Collection([$error]));
        } else {
            $this->notifications->get($scope)->push($error);
        }
    }

    /**
     * Magic call method
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $reflection_class = new \ReflectionClass($this->notifications);
        if($reflection_class->hasMethod($method)){
            $reflection_method = $reflection_class->getMethod($method);
            return $reflection_method->invokeArgs($this->notifications, $args);
        }
    }

    /**
     * Get scope related errors
     *
     * @param $scope
     *
     * @return Collection
     */
    public function get($scope = null)
    {
        return empty($scope) ? $this->notifications : $this->notifications->get($scope);
    }

    /**
     * To json method
     *
     * @return json
     */
    public function toJson()
    {
        return $this->notifications->toJson();
    }
}
