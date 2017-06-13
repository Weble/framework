<?php

namespace Zoolanders\Framework\Cache;

use Zoolanders\Framework\Container\Container;

/**
 * Cache class with alternative PHP cache (APC) storage.
 *
 * @author
 */
class Apc implements CacheInterface
{

    /**
     * @var string $prefix
     */
    protected $prefix;

    /**
     * @var bool|null
     */
    protected $lifetime = false;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param string $prefix The prefix for cache index keys.
     */
    public function __construct($prefix = null, $lifetime = null)
    {
        $this->container = Container::getInstance();

        $this->prefix = $prefix === null ? md5(__FILE__) : $prefix;
        $this->lifetime = $lifetime;
    }

    public function get($id)
    {
        if ($data = apc_fetch(sprintf('%s-%s', $this->prefix, $id))) {
            if ($entry = @unserialize($data) and is_array($entry)) {
                if ($this->lifetime && (time() - $entry['time']) > $this->lifetime) {
                    return null;
                }
                return $entry['data'];
            }
        }

        return null;
    }

    public function set($id, $data)
    {
        apc_store(sprintf('%s-%s', $this->prefix, $id), serialize(array('data' => $data, 'time' => time())));
        return $this;
    }

    public function clear()
    {
        $cache = new \APCIterator('user', '/^' . preg_quote($this->prefix, '/') . '-/');

        foreach ($cache as $entry) {
            apc_delete($entry['key']);
        }

        return $this;
    }

    public function check()
    {
        return true;
    }

    public function save()
    {
        return $this;
    }
}
