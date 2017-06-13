<?php

namespace Zoolanders\Framework\Tree;

/**
 * Base class to deal with tree structures
 */
class Tree
{

    /**
     * The root node for the tree
     *
     * @var object
     * @since 2.0
     */
    protected $root;

    /**
     * The class name
     *
     * @var string
     * @since 2.0
     */
    protected $itemClass;

    /**
     * A list of filter methods to filter the tree
     *
     * @var array
     * @since 2.0
     */
    protected $filters = array();

    /**
     * Class Constructor
     *
     * @param string $itemClass The name of the class we're dealing with
     * @since 2.0
     */
    public function __construct($itemClass = null)
    {
        if ($itemClass == null) {
            $itemClass = 'Item';
        }

        $this->root = new $itemClass;
        $this->itemClass = $itemClass;
    }

    /**
     * Add a tree filter
     *
     * @param string $filter Method name
     * @param array $args The list of arguments for the method
     *
     * @return Tree $this for chaining support
     *
     * @since 2.0
     */
    public function addFilter($filter, $args = array())
    {
        $this->filters[] = compact('filter', 'args');

        return $this;
    }

    /**
     * Execute the filters on all the tree
     *
     * @return AppTree $this for chaining support
     *
     * @since 2.0
     */
    public function applyFilter()
    {

        foreach ($this->filters as $filter) {
            $this->root->filter($filter['filter'], $filter['args']);
        }

        return $this;
    }

    /**
     * Delegate the method calls to the AppTreeItem class
     *
     * @param  string $method Method name
     * @param  array $args List of arguments
     *
     * @return mixed        Result of the method
     *
     * @since 2.0
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->root, $method), $args);
    }
}
