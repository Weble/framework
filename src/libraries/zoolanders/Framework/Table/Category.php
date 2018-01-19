<?php

namespace Zoolanders\Framework\Table;

class Category extends \CategoryTable
{
    /***
     * Category constructor.
     * @param \App $app
     */
    public function __construct ($app)
    {
        parent::__construct($app);

        $this->class = '\Category';
    }

    /**
     * @param $ids
     * @param bool $published
     * @return array
     */
    public function getById ($ids, $published = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getById($ids, $published);
        }

        $ids = array_filter((array)$ids);
        if (empty($ids)) {
            return array();
        }
        $ids = array_combine($ids, $ids);
        $objects = array_intersect_key($this->_objects, $ids);
        $ids = array_diff_key($ids, $objects);

        if (!empty($ids)) {
            $where = "id IN (" . implode(",", $ids) . ")" . ($published == true ? " AND published = 1" : "");
            $objects += $this->all(array('conditions' => $where));
        }

        usort($objects, create_function('$a, $b', 'if ($a->ordering == $b->ordering) { return 0; } return ($a->ordering < $b->ordering) ? -1 : 1;'));

        return $objects;
    }

    /**
     * @param $application_id
     * @param bool $published
     * @param bool $item_count
     * @param null $user
     * @return array
     */
    public function getAll ($application_id, $published = false, $item_count = false, $user = null)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getAll($application_id, $published, $item_count, $user);
        }

        $application_id = (int)$application_id;
        $language = \JFactory::getLanguage()->getTag();

        if ($item_count) {

            $db = $this->database;
            $db->query('SET SESSION group_concat_max_len = 1048576');

            $select = 'c.*, GROUP_CONCAT(DISTINCT ci.item_id) as item_ids';
            $from = $this->name . ' as c  USE INDEX (APPLICATIONID_ID_INDEX2) LEFT JOIN ' . ZOO_TABLE_CATEGORY_ITEM . ' as ci ON ci.category_id = c.id';

            if ($published) {

                // get dates
                $date = $this->app->date->create();
                $now = $db->Quote($date->toSQL());
                $null = $db->Quote($db->getNullDate());

                $select = 'c.*, GROUP_CONCAT(DISTINCT i.id) as item_ids';

                $from = $this->name . ' as c  USE INDEX (APPLICATIONID_ID_INDEX) LEFT JOIN ' . ZOO_TABLE_CATEGORY_ITEM . ' as ci ON ci.category_id = c.id'
                    . ' LEFT JOIN ' . ZOO_TABLE_ITEM . ' AS i USE INDEX (MULTI_INDEX2) ON ci.item_id = i.id'
                    . ' AND i.' . $this->app->user->getDBAccessString($user)
                    . ' AND i.state = 1'
                    . ' AND (i.publish_up = ' . $null . ' OR i.publish_up <= ' . $now . ')'
                    . ' AND (i.publish_down = ' . $null . ' OR i.publish_down >= ' . $now . ')';
            }

            $where = [
                'c.application_id = ?'
            ];

            if ($published == true) {
                $from .= " LEFT JOIN #__zoo_zl_category_languages AS l ON c.id = l.category_id";
                $where[] = 'c.published = 1';
                $where[] = '((l.language LIKE ' . $db->q($language) . ' AND l.enabled = 1) OR l.language IS NULL)';
            }

            $conditions = array(implode(" AND ", $where), $application_id);
            $group = 'c.id';

            $categories = $this->all(compact('select', 'from', 'conditions', 'group'));

            // sort categories
            uasort($categories, create_function('$a, $b', '
				if ($a->ordering == $b->ordering) {
					return 0;
				}
				return ($a->ordering < $b->ordering) ? -1 : 1;'
            ));

        } else {
            $where = [
                "application_id = ?"
            ];


            if ($published) {
                $where[] = "published = 1";
                $where[] = "((l.language LIKE " . $this->database->q($language) . " AND l.enabled = 1) OR l.language IS NULL)";
            }

            $from = $this->name . ' as c';
            $from .= " LEFT JOIN #__zoo_zl_category_languages AS l ON c.id = l.category_id";

            $categories = $this->all(
                array(
                    'conditions' => array(implode(" AND ", $where), $application_id), 'order' => 'ordering',
                    'from' => $from
                )
            );
        }

        return $categories;
    }

    /**
     * @param $item_id
     * @param bool $published
     * @return array
     */
    public function getByItemId ($item_id, $published = false)
    {
        if (\JFactory::getApplication()->isAdmin()) {
            return parent::getByItemId();
        }

        $language = \JFactory::getLanguage()->getTag();
        $db = $this->database;

        $query = 'SELECT b.*'
            . ' FROM ' . ZOO_TABLE_CATEGORY_ITEM . ' AS a'
            . ' JOIN ' . $this->name . ' AS b ON b.id = a.category_id'
            . ' LEFT JOIN #__zoo_zl_category_languages AS l ON c.id = l.category_id'
            . ' WHERE a.item_id=' . (int)$item_id
            . ($published == true ? " AND b.published = 1" . ' AND ((l.language LIKE ' . $db->q($language) . ' AND l.enabled = 1) OR l.language IS NULL)' : "");

        return $this->_queryObjectList($query, $this->key);
    }
}
