<?php

namespace Zoolanders\Framework\Menu;

use Zoolanders\Framework\Tree\Tree;

class Menu extends Tree
{

    /**
     * The name of the menu
     *
     * @var string
     * @since 2.0
     */
    protected $name;

    /**
     * Menu constructor.
     * @param string $name
     * @param bool $hidden
     */
    public function __construct($name, $hidden = false)
    {
        parent::__construct('\\Zoolanders\\Framework\\Menu\\Item');

        $this->name = $name;

        $this->root->setHidden($hidden);
    }

    /**
     * Render the menu
     * @param   array $decorators an eventual list of decorators to call
     * @return string The html code for the menu
     *
     * @since 2.0
     */
    public function render(array $decorators = [])
    {
        // create html
        $html = '<ul>';
        foreach ($this->root->getChildren() as $child) {
            $html .= $child->render($this);
        }
        $html .= '</ul>';

        // decorator callbacks ?
        if ($decorators) {
            // parse html
            if ($xml = simplexml_load_string($html)) {
                foreach ($decorators as $decorator) {
                    $this->map($xml, $decorator);
                }

                $html = $xml->asXML();
            }
        }

        return $html;
    }

    /**
     * Call a method on evey child of the tree
     *
     * @param  \SimpleXMLElement $xml The xml to traverse
     * @param  DecoratorInterface $decorator The decorator to call on each child
     * @param  array $args The arguments to pass on to the callback
     *
     * @since 2.0
     */
    protected function map(\SimpleXMLElement $xml, $decorator, $args = array())
    {
        // init level
        if (!isset($args['level'])) {
            $args['level'] = 0;
        }

        // call function
        $decorator->index($xml, $args);

        // raise level
        $args['level']++;

        // map to all children
        $children = $xml->children();
        if ($n = count($children)) {
            for ($i = 0; $i < $n; $i++) {
                $this->map($children[$i], $decorator, $args);
            }
        }
    }

    /**
     * Renders a menu in json format
     * @return mixed|string
     */
    public function renderJSON()
    {
        $menu = array();
        foreach ($this->getChildren() as $item) {
            $menu[] = array(
                'name' => $item->getName(),
                'link' => html_entity_decode(\JRoute::_($item->getLink()))
            );
        }

        return json_encode($menu);
    }
}
