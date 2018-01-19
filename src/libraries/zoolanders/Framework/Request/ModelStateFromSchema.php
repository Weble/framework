<?php

namespace Zoolanders\Framework\Request;

use Zoolanders\Framework\Model;
use Zoolanders\Framework\Schema\Schema;
use Zoolanders\Framework\Request\Json\ListFromSchema;

/**
 * Trait ModelStateFromSchema
 * @package Zoolanders\Zooadmin\Repository
 */
trait ModelStateFromSchema
{
    use RequestFromSchema;

    /**
     * @param Model\Database $model
     * @param Schema $schema
     * @param RequestInterface $request
     */
    public function applyModelStateFromSchema(Model\Database &$model, Schema $schema, RequestInterface $request)
    {
        $parameters = $this->getRequestParametersFromSchema($request, $schema);

        $properties = array_keys($schema->getProperties());

        $sorting = $parameters[ListFromSchema::KEYWORD_SORT];
        $page = $parameters[ListFromSchema::KEYWORD_PAGE];
        $perPage = $parameters[ListFromSchema::KEYWORD_PER_PAGE];
        $filters = $parameters[ListFromSchema::KEYWORD_FILTER];

        $this->applyModelFields($model, $properties);
        $this->applyModelSorting($model, $sorting);
        $this->applyModelPagination($model, $page, $perPage);
        $this->applyModelFilters($model, $filters);
    }

    /**
     * @param Model\Database $model
     * @param $fields
     */
    public function applyModelFields(Model\Database &$model, $fields)
    {
        $fields = array_intersect($fields, array_keys($model->getTable()->getTableColumns()));
        $model->fields($fields);
    }

    /**
     * @param Model\Database $model
     * @param $sorting
     */
    public function applyModelSorting(Model\Database &$model, $sorting)
    {
        if ($sorting) {
            $model->orderBy($sorting);
        }
    }

    /**
     * @param Model\Database $model
     * @param $filters
     */
    public function applyModelFilters(Model\Database &$model, $filters)
    {
        foreach ($filters as $key => $value) {
            $model->$key($value);
        }
    }

    /**
     * @param Model\Database $model
     * @param $page
     * @param $perPage
     */
    public function applyModelPagination(Model\Database &$model, $page, $perPage)
    {
        $model->paginate($page, $perPage);
    }
}
