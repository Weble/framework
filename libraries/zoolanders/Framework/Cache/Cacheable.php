<?php

namespace Zoolanders\Framework\Cache;

use League\Flysystem\Cached\CacheInterface;

trait Cacheable
{
    /**
     * @var
     */
    protected $cache;

    /**
     * @var
     */
    protected $cacheIndex;

    /**
     * @var int
     */
    protected $cacheTime = 3600;

    /**
     * @param $key
     * @param callable $fallback
     * @param array $args
     * @return mixed
     */
    public function cache ($key, Callable $fallback, array $args = [])
    {
        $cached = $this->getCache()->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $cached = call_user_func_array($fallback, $args);
        $this->getCache()->set($key, $cached);

        return $cached;
    }

    /**
     * @return Cache
     */
    public function getCache ()
    {
        if ($this->cache) {
            return $this->cache;
        }

        $this->cache = new Cache($this->getCacheIndex(), true, $this->cacheTime);

        return $this->cache;
    }

    /**
     * @param CacheInterface $cache
     */
    public function setCache (CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getCacheIndex ()
    {
        if (!$this->cacheIndex) {
            $this->cacheIndex = str_replace("\\", "", get_class($this)) . 'Cache';
        }

        return $this->cacheIndex;
    }
}
