<?php

namespace Zoolanders\Framework\Request;

use Zoolanders\Framework\Schema\Link;

trait ValidateRequest
{
    /**
     * @param RequestInterface $request
     * @param Link $schema
     * @return \League\JsonGuard\Validator
     */
    public static function validateJsonRequest (RequestInterface $request, Link $schema)
    {
        $data = new \stdClass();

        foreach ($schema->getProperties() as $key => $property) {
            $type = self::getValidateRequestType($property->type);
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
     * @param $schemaType
     * @return string
     */
    protected static function getValidateRequestType ($schemaType)
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