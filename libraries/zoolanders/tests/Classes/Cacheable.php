<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Cache\Cache;

/**
 * Class Cacheable
 * Used for testing cacheable functionality
 *
 * @package ZFTests\Classes
 */
trait Cacheable
{
    /**
     * @var Cache storage obj
     */
    public $cache;

    /**
     * @var string  Cache test file location
     */
    public $cache_file = JPATH_CACHE . '/cachetest.dat';

    /**
     * Init cache file
     */
    public function initCache(){
        $this->cache = new Cache($this->cache_file);
    }
}
