<?php

namespace Zoolanders\Framework\Collection;

use Closure;

interface CollectionInterface extends \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all ();

    /**
     * Collapse the collection items into a single array.
     *
     * @return Collection
     */
    public function collapse ();

    /**
     * Diff the collection with the given items.
     *
     * @param  Collection|array $items
     *
     * @return Collection
     */
    public function diff ($items);

    /**
     * Execute a callback over each item.
     *
     * @param  Closure $callback
     *
     * @return Collection
     */
    public function each (Closure $callback);

    /**
     * Fetch a nested element of the collection.
     *
     * @param  string $key
     *
     * @return Collection
     */
    public function fetch ($key);

    /**
     * Run a filter over each of the items.
     *
     * @param  Closure $callback
     *
     * @return Collection
     */
    public function filter (Closure $callback);

    /**
     * Get the first item from the collection.
     *
     * @param  \Closure $callback
     * @param  mixed $default
     *
     * @return mixed|null
     */
    public function first (Closure $callback = null, $default = null);

    /**
     * Get a flattened array of the items in the collection.
     *
     * @return array
     */
    public function flatten ();

    /**
     * Remove an item from the collection by key.
     *
     * @param  mixed $key
     *
     * @return void
     */
    public function forget ($key);

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed $key
     * @param  mixed $default
     *
     * @return mixed
     */
    public function get ($key, $default = null);

    /**
     * Group an associative array by a field or Closure value.
     *
     * @param  callable|string $groupBy
     *
     * @return Collection
     */
    public function groupBy ($groupBy);

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function has ($key);

    /**
     * Concatenate values of a given key as a string.
     *
     * @param  string $value
     * @param  string $glue
     *
     * @return string
     */
    public function implode ($value, $glue = null);

    /**
     * Intersect the collection with the given items.
     *
     * @param  Collection|array $items
     *
     * @return Collection
     */
    public function intersect ($items);

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty ();

    /**
     * Get the last item from the collection.
     *
     * @return mixed|null
     */
    public function last ();

    /**
     * Get an array with the values of a given key.
     *
     * @param  string $value
     * @param  string $key
     *
     * @return array
     */
    public function lists ($value, $key = null);

    /**
     * Run a map over each of the items.
     *
     * @param  Closure $callback
     *
     * @return Collection
     */
    public function map (Closure $callback);

    /**
     * Merge the collection with the given items.
     *
     * @param  Collection|array $items
     *
     * @return Collection
     */
    public function merge ($items);

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed|null
     */
    public function pop ();

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  mixed $value
     *
     * @return void
     */
    public function prepend ($value);

    /**
     * Push an item onto the end of the collection.
     *
     * @param  mixed $value
     *
     * @return void
     */
    public function push ($value);

    /**
     * Put an item in the collection by key.
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return void
     */
    public function put ($key, $value);

    /**
     * Reduce the collection to a single value.
     *
     * @param  callable $callback
     * @param  mixed $initial
     *
     * @return mixed
     */
    public function reduce ($callback, $initial = null);

    /**
     * Get one or more items randomly from the collection.
     *
     * @param  int $amount
     *
     * @return mixed
     */
    public function random ($amount = 1);

    /**
     * Reverse items order.
     *
     * @return Collection
     */
    public function reverse ();

    /**
     * Get and remove the first item from the collection.
     *
     * @return mixed|null
     */
    public function shift ();

    /**
     * Slice the underlying collection array.
     *
     * @param  int $offset
     * @param  int $length
     * @param  bool $preserveKeys
     *
     * @return Collection
     */
    public function slice ($offset, $length = null, $preserveKeys = false);

    /**
     * Sort through each item with a callback.
     *
     * @param  Closure $callback
     *
     * @return Collection
     */
    public function sort (Closure $callback);

    /**
     * Sort the collection using the given Closure.
     *
     * @param  \Closure|string $callback
     * @param  int $options
     * @param  bool $descending
     *
     * @return Collection
     */
    public function sortBy ($callback, $options = SORT_REGULAR, $descending = false);

    /**
     * Sort the collection in descending order using the given Closure.
     *
     * @param  \Closure|string $callback
     * @param  int $options
     *
     * @return Collection
     */
    public function sortByDesc ($callback, $options = SORT_REGULAR);

    /**
     * Splice portion of the underlying collection array.
     *
     * @param  int $offset
     * @param  int $length
     * @param  mixed $replacement
     *
     * @return Collection
     */
    public function splice ($offset, $length = 0, $replacement = array());

    /**
     * Get the sum of the given values.
     *
     * @param  \Closure|string $callback
     *
     * @return mixed
     */
    public function sum ($callback);

    /**
     * Take the first or last {$limit} items.
     *
     * @param  int $limit
     *
     * @return Collection
     */
    public function take ($limit = null);

    /**
     * Resets the Collection (removes all items)
     *
     * @return  Collection
     */
    public function reset ();

    /**
     * Transform each item in the collection using a callback.
     *
     * @param  callable $callback
     *
     * @return Collection
     */
    public function transform ($callback);

    /**
     * Return only unique items from the collection array.
     *
     * @return Collection
     */
    public function unique ();

    /**
     * Reset the keys on the underlying array.
     *
     * @return Collection
     */
    public function values ();

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray ();

    /**
     * Get the collection of items as JSON.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson ($options = 0);

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count ();
}
