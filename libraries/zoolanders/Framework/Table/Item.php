<?php

namespace Zoolanders\Framework\Table;

class Item extends \ItemTable
{
    /***
     * Item constructor.
     * @param \App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->class = '\Item';
    }

    /**
     * @param $application_id
     * @param $category_id
     * @param bool $published
     * @param null $user
     * @param string $orderby
     * @param int $offset
     * @param int $limit
     * @param bool $ignore_order_priority
     * @return array
     */
    public function getByCategory($application_id, $category_id, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0, $ignore_order_priority = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getByCategory($application_id, $category_id, $published, $user, $orderby, $offset, $limit, $ignore_order_priority);
        }

        // get database
        $db = $this->database;

        // get dates
        $date = $this->app->date->create();
        $now = $db->Quote($date->toSQL());
        $null = $db->Quote($db->getNullDate());
        $language = \JFactory::getLanguage()->getTag();

        // get item ordering
        list($join, $order) = $this->_getItemOrder($orderby, $ignore_order_priority);

        $query = "SELECT a.*"
            . " FROM " . $this->name . " AS a"
            . " LEFT JOIN " . ZOO_TABLE_CATEGORY_ITEM . " AS b ON a.id = b.item_id"
            . ($published == true ? " LEFT JOIN #__zoo_zl_item_languages AS l ON a.id = l.item_id" : "")
            . ($join ? $join : "")
            . " WHERE a.application_id = " . (int)$application_id
            . " AND a." . $this->app->user->getDBAccessString($user)
            . ($published == true ? " AND a.state = 1"
                . " AND ((l.language LIKE " . $db->q($language) . " AND l.enabled = 1) OR l.language IS NULL)"
                . " AND (a.publish_up = " . $null . " OR a.publish_up <= " . $now . ")"
                . " AND (a.publish_down = " . $null . " OR a.publish_down >= " . $now . ")" : "")
            . " AND b.category_id " . (is_array($category_id) ? " IN (" . implode(",", $category_id) . ")" : " = " . (int)$category_id)
            . " GROUP BY a.id"
            . ($order ? " ORDER BY " . $order : "")
            . ($limit ? " LIMIT " . (int)$offset . "," . (int)$limit : "");

        $data = $this->_queryObjectList($query);

        return $data;
    }

    /**
     * @param $ids
     * @param bool $published
     * @param null $user
     * @param string $orderby
     * @param bool $ignore_order_priority
     * @return array
     */
    public function getByIds($ids, $published = false, $user = null, $orderby = '', $ignore_order_priority = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getByIds($ids, $published, $user, $orderby, $ignore_order_priority);
        }

        $ids = (array)$ids;

        if (empty($ids)) {
            return array();
        }

        // get database
        $db = $this->database;

        // get dates
        $date = $this->app->date->create();
        $now = $db->Quote($date->toSQL());
        $null = $db->Quote($db->getNullDate());
        $language = \JFactory::getLanguage()->getTag();

        // get item ordering
        list($join, $order) = $this->_getItemOrder($orderby, $ignore_order_priority);

        $query = "SELECT a.*"
            . " FROM " . $this->name . " AS a"
            . " LEFT JOIN #__zoo_zl_item_languages AS l ON a.id = l.item_id"
            . ($join ? $join : "")
            . " WHERE a.id IN (" . implode(",", $ids) . ")"
            . " AND a." . $this->app->user->getDBAccessString($user)
            . ($published == true ? " AND a.state = 1"
                . " AND ((l.language LIKE " . $db->q($language) . " AND l.enabled = 1) OR l.language IS NULL)"
                . " AND (a.publish_up = " . $null . " OR a.publish_up <= " . $now . ")"
                . " AND (a.publish_down = " . $null . " OR a.publish_down >= " . $now . ")" : "")
            . ($order ? " ORDER BY " . $order : "");

        return $this->_queryObjectList($query);
    }

    /**
     * @param $application_id
     * @param $char
     * @param bool $not_in
     * @param bool $published
     * @param null $user
     * @param string $orderby
     * @param int $offset
     * @param int $limit
     * @param bool $ignore_order_priority
     * @return array
     */
    public function getByCharacter($application_id, $char, $not_in = false, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0, $ignore_order_priority = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getByCharacter($application_id, $char, $not_in, $published, $user, $orderby, $offset, $limit, $ignore_order_priority);
        }

        // get database
        $db = $this->database;

        // get dates
        $date = $this->app->date->create();
        $now = $db->Quote($date->toSQL());
        $null = $db->Quote($db->getNullDate());
        $language = \JFactory::getLanguage()->getTag();

        // escape and quote character array
        if (is_array($char)) {
            foreach ($char as $key => $val) {
                $char[$key] = "'" . $db->escape($val) . "'";
            }
        }

        // get item ordering
        list($join, $order) = $this->_getItemOrder($orderby, $ignore_order_priority);

        $query = "SELECT a.*"
            . " FROM " . ZOO_TABLE_CATEGORY_ITEM . " AS ci"
            . " JOIN " . $this->name . " AS a ON a.id = ci.item_id"
            . " LEFT JOIN #__zoo_zl_item_languages AS l ON a.id = l.item_id"
            . ($join ? $join : "")
            . " WHERE a.application_id = " . (int)$application_id
            . " AND a." . $this->app->user->getDBAccessString($user)
            . ($published == true ? " AND a.state = 1"
                . " AND ((l.language LIKE " . $db->q($language) . " AND l.enabled = 1) OR l.language IS NULL)"
                . " AND (a.publish_up = " . $null . " OR a.publish_up <= " . $now . ")"
                . " AND (a.publish_down = " . $null . " OR a.publish_down >= " . $now . ")" : "")
            . " AND BINARY LOWER(LEFT(a.name, 1)) " . (is_array($char) ? ($not_in ? "NOT" : null) . " IN (" . implode(",", $char) . ")" : " = '" . $db->escape($char) . "'")
            . ($order ? " ORDER BY " . $order : "")
            . ($limit ? " LIMIT " . (int)$offset . "," . (int)$limit : "");

        return $this->_queryObjectList($query);
    }

    /**
     * @param $application_id
     * @param $tag
     * @param bool $published
     * @param null $user
     * @param string $orderby
     * @param int $offset
     * @param int $limit
     * @param bool $ignore_order_priority
     * @return array
     */
    public function getByTag($application_id, $tag, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0, $ignore_order_priority = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getByTag($application_id, $tag, $published, $user, $orderby, $offset, $limit, $ignore_order_priority);
        }

        // get database
        $db = $this->database;

        // get dates
        $date = $this->app->date->create();
        $now = $db->Quote($date->toSQL());
        $null = $db->Quote($db->getNullDate());
        $language = \JFactory::getLanguage()->getTag();

        // get item ordering
        list($join, $order) = $this->_getItemOrder($orderby, $ignore_order_priority);

        $query = "SELECT a.*"
            . " FROM " . $this->name . " AS a "
            . " LEFT JOIN " . ZOO_TABLE_TAG . " AS b ON a.id = b.item_id"
            . " LEFT JOIN #__zoo_zl_item_languages AS l ON a.id = l.item_id"
            . ($join ? $join : "")
            . " WHERE a.application_id = " . (int)$application_id
            . " AND b.name = '" . $db->escape($tag) . "'"
            . " AND a." . $this->app->user->getDBAccessString($user)
            . ($published == true ? " AND a.state = 1"
                . " AND ((l.language LIKE " . $db->q($language) . " AND l.enabled = 1) OR l.language IS NULL)"
                . " AND (a.publish_up = " . $null . " OR a.publish_up <= " . $now . ")"
                . " AND (a.publish_down = " . $null . " OR a.publish_down >= " . $now . ")" : "")
            . " GROUP BY a.id"
            . ($order ? " ORDER BY " . $order : "")
            . ($limit ? " LIMIT " . (int)$offset . "," . (int)$limit : "");

        return $this->_queryObjectList($query);
    }

    /**
     * @param $type_id
     * @param bool $application_id
     * @param bool $published
     * @param null $user
     * @param string $orderby
     * @param int $offset
     * @param int $limit
     * @param bool $ignore_order_priority
     * @return array
     */
    public function getByType($type_id, $application_id = false, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0, $ignore_order_priority = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getByType($type_id, $application_id, $published, $user, $orderby, $offset, $limit, $ignore_order_priority);
        }

        // get database
        $db = $this->database;

        // get dates
        $date = $this->app->date->create();
        $now = $db->Quote($date->toSQL());
        $null = $db->Quote($db->getNullDate());
        $language = \JFactory::getLanguage()->getTag();

        // get item ordering
        list($join, $order) = $this->_getItemOrder($orderby, $ignore_order_priority);

        $query = "SELECT a.*"
            . " FROM " . $this->name . " AS a"
            . " LEFT JOIN #__zoo_zl_item_languages AS l ON a.id = l.item_id"
            . ($join ? $join : "")
            . " WHERE a.type = " . $db->Quote($type_id)
            . ($application_id !== false ? " AND a.application_id = " . (int)$application_id : "")
            . " AND a." . $this->app->user->getDBAccessString($user)
            . ($published == true ? " AND a.state = 1"
                . " AND ((l.language LIKE " . $db->q($language) . " AND l.enabled = 1) OR l.language IS NULL)"
                . " AND (a.publish_up = " . $null . " OR a.publish_up <= " . $now . ")"
                . " AND (a.publish_down = " . $null . " OR a.publish_down >= " . $now . ")" : "")
            . " GROUP BY a.id"
            . ($order ? " ORDER BY " . $order : "")
            . ($limit ? " LIMIT " . (int)$offset . "," . (int)$limit : "");

        return $this->_queryObjectList($query);
    }
}
