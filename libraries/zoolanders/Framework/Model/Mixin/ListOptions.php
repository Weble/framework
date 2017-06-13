<?php

namespace Zoolanders\Framework\Model\Mixin;

/**
 * Class ListOptions
 * For models, allowed to operate with lists of items
 * Contains methods to operate with list options like sorting, pagination and filters.
 *
 * @package Zoolanders\Framework\Model\Mixin
 */
trait ListOptions
{
    /**
     * Set filter value:
     *
     * @param   string
     * @param   value
     */
    public function setFilter($param, $value)
    {
        $this->setState('filter_' . $param, $value);
    }

    /**
     * Get filter value or null
     *
     * @param   string
     * @param           Default value
     *
     * @return mixed
     */
    public function getFilter($param, $default = null)
    {
        return $this->getState('filter_' . $param, $default);
    }

    /**
     * Remove filter param from set:
     *
     * @param   string FIlter name
     */
    public function dropFilter($param)
    {
        $this->setState('filter_' . $param, null);
    }

    /**
     * Get all registered filters
     *
     * @return array
     */
    public function getFilters()
    {
        $state = array_filter($this->state->getArrayCopy(), function($value, $key) {
            return preg_match('/^filter_/i', $key) && (null!==$value);
        }, ARRAY_FILTER_USE_BOTH);

        return $state;
    }

    /**
     * Set sorting options
     *
     * @param $field
     * @param string $order
     */
    public function setSorting($field, $order = 'asc')
    {
        $sorting = $this->getState('sort', []);
        $sorting[$field] = $order;
        $this->setState('sort', $sorting);
    }

    /**
     * Get sorting options
     *
     * @return array
     */
    public function getSorting()
    {
        return $this->getState('sort');
    }

    /**
     * Export list options as array
     *
     * @return  array
     */
    public function listOptionsToArray()
    {
        $perPage = $this->getState('limit', 30); //default items per page value
        $page = $this->getState('offset', 0);
        $page = $page ? floor($page / $perPage)+1 : 1;

        $state = [
            'filters' => $this->getFilters(),
            'page' => $page,
            'perPage' => $perPage
        ];

        $sorting = $this->getSorting();
        if(!empty($sorting)){
            $state['sort'] = $sorting;
        }

        return $state;
    }
}
