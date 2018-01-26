<?php

namespace Zoolanders\Framework\Request\Json;

use Zoolanders\Framework\Request\JsonRequest;

/**
 * Class ListFromSchema
 * @package Zoolanders\Framework\Request\Json
 */
class ListFromSchema extends JsonRequest
{
    /**
     * Fixed keys
     */
    const KEYWORD_TOTAL = 'total';
    const KEYWORD_PER_PAGE = 'perPage';
    const KEYWORD_PAGE = 'page';
    const KEYWORD_SORT = 'sort';
    const KEYWORD_DATA = 'data';
    const KEYWORD_FILTER = 'filter';

}