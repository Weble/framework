<?php

namespace Zoolanders\Framework\Cache;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Filesystem;

/**
 * The cache class.
 */
class Cache implements CacheInterface
{

    /**
     * Path to cache file
     *
     * @var string
     * @since 2.0
     */
    protected $file = 'config.txt';

    /**
     * Path to cache file
     *
     * @var array
     * @since 2.0
     */
    protected $items = array();

    /**
     * marks cache dirty
     *
     * @var boolean
     * @since 2.0
     */
    protected $dirty = false;

    /**
     * The cached items
     *
     * @var boolean
     * @since 2.0
     */
    protected $hash = true;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Class constructor
     *
     * @param string $file Path to cache file
     * @param boolean $hash Wether the key should be hashed
     * @param int $lifetime The values lifetime
     * @since 2.0
     */
    public function __construct ($file, $hash = true, $lifetime = null)
    {
        $this->filesystem = Container::getInstance()->filesystem;

        // if cache file doesn't exist, create it
        if (!$this->filesystem->has($file)) {
            $this->filesystem->write($file, '');
        }

        // set file and parse it
        $this->file = $file;
        $this->hash = $hash;
        $this->parse();

        // clear out of date values
        if ($lifetime) {
            $lifetime = (int)$lifetime;
            $remove = array();
            foreach ($this->items as $key => $value) {
                if ((time() - $value['timestamp']) > $lifetime) {
                    $remove[] = $key;
                }
            }
            foreach ($remove as $key) {
                unset($this->items[$key]);
            }
        }
    }

    /**
     * Check if the cache file is writable and readable
     *
     * @return boolean If the cache can be used
     *
     * @since 2.0
     */
    public function check ()
    {
        return $this->filesystem->has($this->file);
    }

    /**
     * Get a cache content
     *
     * @param  string $key The key
     *
     * @return mixed      The cache content
     *
     * @since 2.0
     */
    public function get ($key)
    {
        if ($this->hash) {
            $key = md5($key);
        }

        if (!array_key_exists($key, $this->items))
            return null;

        return $this->items[$key]['value'];
    }

    /**
     * Set a cache content
     *
     * @param string $key The key
     * @param mixed $value The value
     *
     * @return Cache $this for chaining support
     *
     * @since 2.0
     */
    public function set ($key, $value)
    {
        if ($this->hash) {
            $key = md5($key);
        }

        if (array_key_exists($key, $this->items) && @$this->items[$key]['value'] == $value)
            return $this;

        $this->items[$key]['value'] = $value;
        $this->items[$key]['timestamp'] = time();
        $this->dirty = true;

        return $this;
    }

    /**
     * Parse the cache file
     *
     * @return Cache $this for chaining support
     *
     * @since 2.0
     */
    protected function parse ()
    {
        $content = $this->filesystem->read($this->file);

        if (!empty($content)) {
            $items = json_decode($content, true);

            if (is_array($items)) {
                $this->items = $items;
            }
        }

        return $this;
    }

    /**
     * Save the cache file if it was changed
     *
     * @return Cache $this for chaining support
     *
     * @since 2.0
     */
    public function save ()
    {
        if ($this->dirty) {
            $data = json_encode($this->items);
            $this->filesystem->put($this->file, $data);
        }

        return $this;
    }

    /**
     * Clear the cache file
     *
     * @return Cache $this for chaining support
     */
    public function clear ()
    {
        $this->items = array();
        $this->dirty = true;

        return $this;
    }
}
