<?php

namespace Zoolanders\Framework\Response;

use Zoolanders\Framework\Schema\Link;

trait ValidateResponse
{
    /**
     * @param ResponseInterface|array $response
     * @param Link $schema
     * @return \League\JsonGuard\Validator
     */
    public static function validateJsonResponse ($response, Link $schema)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getContent();
        }

        $targetSchema = $schema->getTargetSchema();

        if ($targetSchema) {
            $validator = $targetSchema->validate($response);
            return $validator;
        }

        return false;
    }
}