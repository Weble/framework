<?php

namespace Zoolanders\Framework\Menu;

interface DecoratorInterface {
    /**
     * Add item index and level to class attribute
     *
     * @param  \SimpleXMLElement $node The node to add the index and level to
     * @param  array $args Callback arguments
     *
     * @since    2.0
     */
    public function index (\SimpleXMLElement $node, $args);
}
