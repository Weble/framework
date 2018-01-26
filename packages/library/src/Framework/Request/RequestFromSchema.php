<?php

namespace Zoolanders\Framework\Request;

use Zoolanders\Framework\Schema\Link;
use Zoolanders\Framework\Schema\Schema;

/**
 * Trait RequestFromSchema
 * @package Zoolanders\Framework\Request
 */
trait RequestFromSchema
{
    /**
     * @param RequestInterface $request
     * @param Link $schema
     * @return \League\JsonGuard\Validator
     */
    public function validateJsonRequest (RequestInterface $request, Link $schema)
    {
        $data = new \stdClass();

        foreach ($schema->getProperties() as $key => $property) {
            $type = $this->getValidateRequestType($property->type);
            $value = $request->get($key, false, $type);

            if ($type == 'object' && is_array($value)) {
                $value = (object) $value;
            }

            if ($value !== false) {
                $data->$key = $value;
            }
        }

        $validator = $schema->validate($data);

        return $validator;
    }

    /**
     * @param RequestInterface $request
     * @param Schema $schema
     * @return array
     */
    public function getRequestParametersFromSchema(RequestInterface $request, Schema $schema)
    {
        $properties = $schema->getProperties();

        $data = [];
        foreach ($properties as $key => $value) {
            $data[$key] = $request->get($key, $value->getDefaultValue(), $this->getValidateRequestType($value->type));
        }

        return $data;
    }

    /**
     * Convert the schema type to the joomla filter type
     * @param $schemaType
     * @return string
     */
    public function getValidateRequestType ($schemaType)
    {
        switch ($schemaType) {
            case 'number':
                $type = 'float';
                break;
            case 'integer':
            case 'int':
                $type = 'int';
                break;
            case 'boolean':
            case 'bool':
                $type = 'bool';
                break;
            case 'array':
                $type = 'array';
                break;
            case 'object':
                $type = 'object';
                break;
            case 'string':
            default:
                $type = 'var';
                break;
        }

        return $type;
    }
}