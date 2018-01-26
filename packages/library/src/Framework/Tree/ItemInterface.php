<?php

namespace Zoolanders\Framework\Tree;

interface ItemInterface
{
    public function getID ();

    public function getParent ();

    public function setParent (ItemInterface $item);

    public function getChildren ();

    public function hasChild ($id, $recursive = false);

    public function hasChildren ();

    public function addChild (ItemInterface $item);

    public function addChildren (array $children);

    public function removeChild (ItemInterface $item);

    public function removeChildById ($id);

    public function getPathway ();

    public function filter ($callback, $args = array());

}
