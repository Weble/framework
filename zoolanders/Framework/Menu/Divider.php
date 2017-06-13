<?php

namespace Zoolanders\Framework\Menu;

/**
 * Represents a Menu Divider
 */
class Divider extends Item
{

    /**
     * Render the single menu item
     *
     * @return string The html for this menu item
     */
    public function render()
    {
        return '<li class="uk-nav-divider"></li>';
    }
}
