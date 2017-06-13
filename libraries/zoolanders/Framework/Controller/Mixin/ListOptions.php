<?php

namespace Zoolanders\Framework\Controller\Mixin;

use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Utils\ListFilterState;

/**
 * Class filterOperative
 * Allows controllers to operate with filtering params
 *
 * Filter config assumed to be array, containing info
 * about data input filter (acc to JInput) name and default value like:
 *
 * [
 *      'state' => [
 *                  'filter' => 'int',
 *                  'default' => null
 *                  ],
 *      'name' => [
 *                  'filter' => 'string'
 *                  'default' => ''
 *                  ]
 * ]
 *
 * @package Zoolanders\Framework\Controller\Mixin
 */
trait ListOptions
{
    /**
     * @var Config that describes filtering params validation and sefault values
     */
    protected $filters_config = [];

    /**
     * Set filter configuration
     *
     * @param array $cfg
     */
    public function setFilterConfig($cfg = [])
    {
        $this->filters_config = $cfg;
    }

    /**
     * Set filter configuration
     *
     * @return array
     */
    public function getFilterConfig()
    {
        return $this->filters_config;
    }

    /**
     * Set filter configuration
     *
     * @return array
     */
    public function getListOptions()
    {
        // ?
    }

    /**
     * Retrieve filtering options from request, and merge with default presets
     *
     * @param Request $request
     *
     * @return void
     */
    public function getListStateFromRequest(Request $request)
    {
        $this->getListOptions();

        if(!empty($this->filters_config)){
            // Filters:
            foreach($this->filters_config as $param => $settings){
                $value = $request->get($param, @$settings['default'], @$settings['filter']);
                if(null === $value){
                    $this->model->dropFilter($param);
                } else {
                    $this->model->setFilter($param, $value);
                }
            }
        }

        // Sorting:
        $sort = $request->get('sort', [], 'array');

        if (!empty($sort)) {
            foreach($sort as $field => $dir){
                $this->model->setSorting($field, $dir);
            }
        }

        // Pagination:
        $this->model->paginate(
            $request->getInt('page', 1),
            $request->getInt('perPage', 30)
        );
    }
}
