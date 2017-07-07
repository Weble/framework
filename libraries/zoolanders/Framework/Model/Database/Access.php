<?php

namespace Zoolanders\Framework\Model\Database;

trait Access
{
    /**
     * @param $query
     * @param null $user
     * @return $this
     */
    protected function filterAccessible ($user = null)
    {
        if (is_null($user)) {
            $user = \JFactory::getUser();
        }

        $field = isset($this->tablePrefix) ? $this->tablePrefix . '.access' : 'access';

        $groups = implode(',', array_unique($user->getAuthorisedViewLevels()));

        $this->where($this->db->qn($field) . ' IN ' . $this->db->q($groups));

        return $this;
    }
}
