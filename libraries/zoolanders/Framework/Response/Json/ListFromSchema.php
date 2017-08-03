<?php

namespace Zoolanders\Framework\Response\Json;

use Zoolanders\Framework\Model;
use Zoolanders\Framework\Response\JsonResponse;
use Zoolanders\Framework\Response\ValidateResponse;
use Zoolanders\Framework\Schema\Link;
use Zoolanders\Framework\Schema\Schema;

class ListFromSchema extends JsonResponse
{
    use ValidateResponse;

    /**
     * Fixed keys
     */
    const KEYWORD_TOTAL = 'total';
    const KEYWORD_PER_PAGE = 'perPage';
    const KEYWORD_PAGE = 'page';
    const KEYWORD_SORT = 'sort';
    const KEYWORD_DATA = 'data';
    const KEYWORD_FILTER = 'filter';

    /**
     * @var Link
     */
    protected $schema;

    /**
     * ListFromSchema constructor.
     * @param array $data
     * @param Model\Database $model
     * @param Link $schema
     */
    public function __construct ($data, Model\Database $model, Link $schema)
    {
        $data = $this->setupDataFromSchema($data, $model, $schema->getTargetSchema());

        $this->schema = $schema;

        parent::__construct($data);
    }

    /**
     * @param array $items
     * @param Model\Database $model
     * @param Schema $schema
     * @return array
     */
    protected function setupDataFromSchema ($items, Model\Database $model, Schema $schema)
    {
        $properties = $schema->getProperties();

        $data = [];

        $offset = $model->getState('offset');
        $limit = $model->getState('limit');

        if ($limit == 0) {
            $limit = 20;
        }

        foreach ($properties as $key => $property) {
            $data[$key] = $property->getDefaultValue();

            // this is the items list
            if ($key == self::KEYWORD_DATA) {
                $data[$key] = $items;
            }

            if ($key == self::KEYWORD_TOTAL) {
                $data[$key] = $model->getTotal();
            }

            if ($key == self::KEYWORD_PER_PAGE) {
                $data[$key] = $limit;
            }

            if ($key == self::KEYWORD_PAGE) {
                $data[$key] = (int) ($offset / $limit) + 1;
            }

            if ($key == self::KEYWORD_SORT) {
                $data[$key] = (object) $model->getOrder();
            }

            if ($key == self::KEYWORD_FILTER) {
                $data[$key] = (object) $model->getState();
            }
        }

        return $data;
    }

    /**
     * @return \League\JsonGuard\Validator
     */
    public function validate ()
    {
        return $this->validateJsonResponse($this->data->getArrayCopy(), $this->schema);
    }
}
