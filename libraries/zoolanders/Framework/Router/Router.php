<?php

namespace Zoolanders\Framework\Router;

use Zoolanders\Framework\Cache\CacheInterface;
use Zoolanders\Framework\Container\Container;

defined('_JEXEC') or die();

/**
 * The ZL router class
 */
abstract class Router
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * The parsed menu items
     * @var array
     */
    protected $menuItems;

    /**
     * The route cache
     * @var CacheInterface
     */
    protected $cache;

    /**
     * The active menu item id
     * @var string
     */
    protected $activeMenuItemId;

    /**
     * Router constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        // set application
        $this->container = $c;

        // cache
        if ($this->container->params->get('cache_routes', false)) {
            // get route cache
            // refreshes after one hour automatically
            $this->cache = $this->container->cache->create($this->container->path->path('cache:') . '/routes', true, 3600, 'apc');

            if (!$this->cache || !$this->cache->check()) {
                $this->cache = null;
            } else {
                $key = json_encode($this->menuItems);

                if (!$this->cache->get($key)) {
                    $this->cache->clear()->set($key, true)->save();
                }
            }
        }

        // save default menu
        if ($menuItem = $this->container->joomla->getMenu()->getActive()) {
            $this->activeMenuItemId = $menuItem->id;
        }
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Route building
     */
    abstract public function buildRoute(&$query, &$segments);

    /**
     * Route parsing
     */
    abstract public function parseRoute(&$segments, &$vars);

    /**
     * Clears the router cache
     */
    public function clearCache()
    {
        if ($this->cache) {
            $this->cache->clear()->save();
        }
    }

    /**
     * Gets this route helpers link base
     *
     * @return string the link base
     */
    public function getLinkBase()
    {
        return 'index.php?option=com_zoolanders';
    }
}
