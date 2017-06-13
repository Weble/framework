<?php

namespace Zoolanders\Framework\Menu;

use Joomla\Utilities\ArrayHelper;
use Zoolanders\Framework\Tree;

/**
 * Class to represent a Menu Item
 *
 * @package Component.Classes
 */
class Item extends Tree\Item
{
    /**
     * Id of the menu item
     *
     * @var int
     * @since 2.0
     */
    protected $id;

    /**
     * Name of the menu item
     *
     * @var string
     * @since 2.0
     */
    protected $name;

    /**
     * Url of the menu item
     *
     * @var string
     * @since 2.0
     */
    protected $link;

    /**
     * Attributes to apply to the item
     *
     * @var array
     * @since 2.0
     */
    protected $attributes;

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * Class contructor
     *
     * @param int $id Id of the menu item
     * @param string $name Name of the menu item
     * @param string $link Link of the menu item
     * @param array $attributes List of attributes
     */
    public function __construct($id = null, $name = '', $link = null, array $attributes = array())
    {
        $this->id = $id;
        $this->name = $name;
        $this->link = $link;
        $this->attributes = $attributes;
    }

    /**
     * @param ItemInterface $item
     * @return $this
     */
    public function addChild(Tree\ItemInterface $item)
    {
        $item->setHidden($this->isHidden());
        parent::addChild($item);

        return $this;
    }

    /**
     * @param boolean $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Get the name of the menu Item
     *
     * @return string The name of the meu item
     *
     * @since 2.0
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the menu item
     *
     * @param string $name The name
     * Item
     * @return Item $this for chaining support
     *
     * @since 2.0
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the id of the menu item
     *
     * @return int The menu item id
     *
     * @since 2.0
     */
    public function getID()
    {
        return $this->id ? $this->id : parent::getId();
    }

    /**
     * Get an attribute for the menu item
     *
     * @param  string $key The key to fetch
     *
     * @return string      The value for the attribute
     *
     * @since 2.0
     */
    public function getAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * Set an attribute for the menu item
     *
     * @param string $key The key
     * @param string $value The value
     *
     * @return Item $this for chaining support
     *
     * @since 2.0
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Render the single menu item
     *
     * @return string The html for this menu item
     */
    public function render()
    {
        $hidemainmenu = $this->isHidden();

        $html = array('<li ' . ArrayHelper::toString($this->attributes) . '>');
        $icon = $this->getAttribute('icon') ? '<i class="uk-icon-' . $this->getAttribute('icon') . '">&#160;</i>' : '';
        $has_children = count($this->getChildren());

        if (!$hidemainmenu) {
            $html[] = '<a href="' . JRoute::_($this->link) . '">' . $icon . $this->getName() . ($has_children ? '&#160;<i class="uk-icon-caret-down">&#160;</i>' : '') . '</a>';
        } else {
            $html[] = '<span>' . $this->getName() . '</span>';
        }

        if ($has_children && !$hidemainmenu) {
            $html[] = '<div class="uk-dropdown uk-dropdown-navbar"><ul>';
            foreach ($this->getChildren() as $child) {
                $html[] = $child->render();
            }
            $html[] = '</ul></div>';
        }

        $html[] = '</li>';

        return implode("\n", $html);
    }

    /**
     * @return null|string
     */
    public function getLink()
    {
        return $this->link;
    }
}
