<?php

namespace Zoolanders\Framework\Menu;

/**
 * A decorator class for the menus
 */
class Decorator implements DecoratorInterface
{
    /**
     * Add item index and level to class attribute
     *
     * @param  \SimpleXMLElement $node The node to add the index and level to
     * @param  array $args Callback arguments
     *
     * @since    2.0
     */
    public function index(\SimpleXMLElement $node, $args)
    {
        if ($node->getName() == 'ul') {
            // set ul level
            $level = ($args['level'] / 2) + 1;
            $node->addAttribute('class', trim($node->attributes()->class . ' level' . $level));

            // set order/first/last for li
            $count = count($node->children());
            foreach ($node->children() as $i => $child) {
                $child->addAttribute('level', $level);
                $child->addAttribute('order', $i + 1);
                if ($i == 0) $child->addAttribute('first', 1);
                if ($i == $count - 1) $child->addAttribute('last', 1);
            }

        }

        if ($node->getName() == 'li') {
            // level and item order
            $css = 'level' . $node->attributes()->level;
            $css .= ' item' . $node->attributes()->order;

            // first, last and parent
            if ($node->attributes()->first) $css .= ' first';
            if ($node->attributes()->last) $css .= ' last';
            if (isset($node->ul)) $css .= ' parent';

            // add li css classes
            $node->attributes()->class = trim($node->attributes()->class . ' ' . $css);

            // add a/span css classes
            $children = $node->children();
            if ($firstChild = $children[0]) {
                $firstChild->addAttribute('class', trim($firstChild->attributes()->class . ' ' . $css));
            }
        }

        unset($node->attributes()->level, $node->attributes()->order, $node->attributes()->first, $node->attributes()->last);

    }
}
