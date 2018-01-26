<?php

namespace Zoolanders\Framework\Service\Filesystem\Adapter;


class Local extends \League\Flysystem\Adapter\Local
{
    /**
     * Constructor.
     *
     * Overrides the base adapter to skip checks on root directory
     *
     * @param string $root
     * @param int    $writeFlags
     * @param int    $linkHandling
     * @param array  $permissions
     */
    public function __construct($root, $writeFlags = LOCK_EX, $linkHandling = self::DISALLOW_LINKS, array $permissions = [])
    {
        $root = @is_link($root) ? realpath($root) : $root;
        $this->permissionMap = array_replace_recursive(static::$permissions, $permissions);

        $this->setPathPrefix($root);
        $this->writeFlags = $writeFlags;
    }
}