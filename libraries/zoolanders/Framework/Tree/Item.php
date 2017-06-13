<?php

namespace Zoolanders\Framework\Tree;

class Item implements ItemInterface
{
    /**
     * The parent item
     *
     * @var Item
     */
    protected $parent;

    /**
     * List of children
     *
     * @var array
     * @since 2.0
     */
    protected $children = array();

    /**
     * Get the item unique id (object hash)
     *
     * @return string Unique id
     *
     * @since 2.0
     */
    public function getID()
    {
        return spl_object_hash($this);
    }

    /**
     * Get the item parent
     *
     * @return Item The parent item
     *
     * @since 2.0
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Drop parent node pointer
     */
    public function disposeParent()
    {
        $this->parent = null;
    }

    /**
     * Set the parent item
     *
     * @param ItemInterface $item The menu item
     *
     * @return Item $this for chaining support
     *
     * @since 2.0
     */
    public function setParent(ItemInterface $item)
    {
        $this->parent = $item;

        return $this;
    }

    /**
     * Get the children list
     *
     * @return array The list of children
     *
     * @since 2.0
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Check if this item has a particular children
     *
     * @param  string $id The item id to find
     * @param  boolean $recursive If the search should go also through the children recursively (default: false)
     *
     * @return boolean            True if the item is a children
     *
     * @since 2.0
     */
    public function hasChild($id, $recursive = false)
    {
        if (isset($this->children[$id])) {
            return true;
        }

        if ($recursive) {
            foreach ($this->children as $child) {
                if ($child->hasChild($id, $recursive)) return true;
            }
        }

        return false;
    }

    /**
     * Count the children of the item
     *
     * @return int The number of children
     *
     * @since 2.0
     */
    public function hasChildren()
    {
        return count($this->children);
    }

    /**
     * Add a child to the item
     *
     * @param ItemInterface $item The item to add
     *
     * @return Item $this for chaining support
     *
     * @since 2.0
     */
    public function addChild(ItemInterface $item)
    {
        $item->setParent($this);
        $this->children[$item->getID()] = $item;

        return $this;
    }

    /**
     * Add a list of items to the item
     *
     * @param array $children The list of items to add
     *
     * @return Item $this for chaining support
     *
     * @since 2.0
     */
    public function addChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * Remove a child
     *
     * @param  ItemInterface $item The child to remove
     *
     * @return Item            $this for chaining support
     *
     * @since 2.0
     */
    public function removeChild(ItemInterface $item)
    {
        $item->disposeParent();
        unset($this->children[$item->getID()]);

        return $this;
    }

    /**
     * Remove the item with the given id
     *
     * @param  string $id The id of the item to remove
     *
     * @return Item     $this for chaining support
     *
     * @since 2.0
     */
    public function removeChildById($id)
    {
        if ($this->hasChild($id)) {
            $this->removeChild($this->children[$id]);
        }

        return $this;
    }

    /**
     * Get the path from the current item to the root of the tree
     *
     * @return array The pathway
     *
     * @since 2.0
     */
    public function getPathway()
    {
        $pathway = array();

        if ($this->parent !== null) {
            $pathway = $this->parent->getPathway();
        }

        $pathway[] = $this;

        return $pathway;
    }

    /**
     * Filter all the items recursively
     *
     * @param  Callable $callback A function to call
     * @param  array $args A list of arguments to pass
     *
     * @since 2.0
     */
    public function filter($callback, $args = array())
    {
        // call filter function
        call_user_func_array($callback, array_merge(array($this), $args));

        // filter all children
        foreach ($this->getChildren() as $child) {
            $child->filter($callback, $args);
        }
    }

}
