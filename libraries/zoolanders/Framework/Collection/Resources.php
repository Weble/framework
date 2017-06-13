<?php

namespace Zoolanders\Framework\Collection;

use Zoolanders\Framework\Utils\HasId;

defined('_JEXEC') or die;

/**
 * Class Resources
 * Inspired by FOF3 Datacollection class by Nicholas K. Dionysopoulos / Akeeba Ltd (https://github.com/akeeba/fof/)
 * @package Zoolanders\Framework\Collection
 */
class Resources extends Collection
{
    /**
     * Create a new collection.
     *
     * @param  array $items
     */
    public function __construct(array $items = array())
    {
        $this->items = [];

        foreach ($items as $item) {
            $this->items[$this->getKey($item)] = $item;
        }
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param  mixed $items
     *
     * @return static
     */
    public static function make($items)
    {
        if (is_null($items)) {
            return new static;
        }

        if ($items instanceof Resources) {
            return $items;
        }

        if ($items instanceof Collection) {
            return new static($items->toArray());
        }

        return new static(is_array($items) ? $items : array($items));
    }

    /**
     * Find a model in the collection by key.
     *
     * @param  mixed $key
     * @param  mixed $default
     *
     * @return HasId
     */
    public function find($key, $default = null)
    {
        return array_first($this->items, function ($itemKey, $item) use ($key) {
            /** @var HasId $item */
            return $this->getKey($item) == $key;

        }, $default);
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getKey($key)
    {
        if ($key instanceof HasId) {
            return $key->getId();
        }
        if (is_object($key) && isset($key->id)) {
            return $key->id;
        }

        if (is_array($key) && isset($key['id'])) {
            return $key['id'];
        }

        return $key;
    }

    /**
     * Remove an item in the collection by key
     *
     * @param  mixed $key
     *
     * @return void
     */
    public function removeById($key)
    {
       $key = $this->getKey($key);

        $index = array_search($key, $this->itemKeys());

        if ($index !== false) {
            unset($this->items[$index]);
        }
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed $item
     *
     * @return Collection
     */
    public function add($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Determine if a key exists in the collection.
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function contains($key)
    {
        return !is_null($this->find($key));
    }

    /**
     * Fetch a nested element of the collection.
     *
     * @param  string $key
     *
     * @return Collection
     */
    public function fetch($key)
    {
        return new static(array_fetch($this->toArray(), $key));
    }

    /**
     * Get the max value of a given key.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function max($key)
    {
        return $this->reduce(function ($result, $item) use ($key) {
            return (is_null($result) || $item->{$key} > $result) ? $item->{$key} : $result;
        });
    }

    /**
     * Get the min value of a given key.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function min($key)
    {
        return $this->reduce(function ($result, $item) use ($key) {
            return (is_null($result) || $item->{$key} < $result) ? $item->{$key} : $result;
        });
    }

    /**
     * Get the array of primary keys
     *
     * @return array
     */
    public function itemKeys()
    {
        return array_map(
            function ($item) {
                return $this->getKey($item);
            },
            $this->items);
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  Collection|array $collection
     * @return Collection
     */
    public function merge($collection)
    {
        $dictionary = $this->getDictionary($this);

        foreach ($collection as $item) {
            $dictionary[$this-getKey($item)] = $item;
        }

        return new static(array_values($dictionary));
    }

    /**
     * Diff the collection with the given items.
     *
     * @param   Collection|array $collection
     * @return  Collection
     */
    public function diff($collection)
    {
        $diff = new static;

        $dictionary = $this->getDictionary($collection);

        foreach ($this->items as $item) {
            /** @var HasId $item */
            if (!isset($dictionary[$this->getKey($item)])) {
                $diff->add($item);
            }
        }

        return $diff;
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param   Collection|array $collection
     *
     * @return  Collection
     */
    public function intersect($collection)
    {
        $intersect = new static;

        $dictionary = $this->getDictionary($collection);

        foreach ($this->items as $item) {
            if (isset($dictionary[$this->getKey($item)])) {
                $intersect->add($item);
            }
        }

        return $intersect;
    }

    /**
     * Return only unique items from the collection.
     *
     * @return Collection
     */
    public function unique()
    {
        $dictionary = $this->getDictionary($this);

        return new static(array_values($dictionary));
    }

    /**
     * Get a dictionary keyed by primary keys.
     *
     * @param  Collection $collection
     *
     * @return array
     */
    protected function getDictionary($collection)
    {
        $dictionary = array();

        foreach ($collection as $value) {
            $dictionary[$this->getKey($value)] = $value;
        }

        return $dictionary;
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return Collection
     */
    public function toCollection()
    {
        return new Collection($this->items);
    }

    /**
     * Magic method which allows you to run a DataModel method to all items in the collection.
     *
     * For example, you can do $collection->save('foobar' => 1) to update the 'foobar' column to 1 across all items in
     * the collection.
     *
     * IMPORTANT: The return value of the method call is not returned back to you!
     *
     * @param string $name The method to call
     * @param array $arguments The arguments to the method
     */
    public function __call($name, $arguments)
    {
        if (!count($this)) {
            return;
        }

        $class = get_class($this->first());

        if (method_exists($class, $name)) {
            foreach ($this as $item) {
                switch (count($arguments)) {
                    case 0:
                        $item->$name();
                        break;

                    case 1:
                        $item->$name($arguments[0]);
                        break;

                    case 2:
                        $item->$name($arguments[0], $arguments[1]);
                        break;

                    case 3:
                        $item->$name($arguments[0], $arguments[1], $arguments[2]);
                        break;

                    case 4:
                        $item->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                        break;

                    case 5:
                        $item->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
                        break;

                    case 6:
                        $item->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
                        break;

                    default:
                        call_user_func_array(array($item, $name), $arguments);
                        break;
                }
            }
        }
    }
}