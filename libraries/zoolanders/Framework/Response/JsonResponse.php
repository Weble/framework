<?php

namespace Zoolanders\Framework\Response;

use Zoolanders\Framework\Data\Json;

/**
 * Class JsonResponse
 * @package Zoolanders\Framework\Response
 */
class JsonResponse extends Response
{
    /**
     * @var string  Content type
     */
    public $type = 'application/json';

    /**
     * JsonResponse constructor
     *
     * @param   int $code
     * @param   $data
     */
    public function __construct ($data = array(), $code = 200)
    {
        $data = new Json($data);

        parent::__construct($data, $code);
    }

    /**
     * @inheritdoc
     */
    public function setContent ($content)
    {
        $this->data = new Json($content);
        return $this;
    }

    /**
     * Bind variable to data
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  object
     */
    public function __set ($key, $value)
    {
        if (null === $this->data) {
            $this->data = new Json();
        }

        $this->data->set($key, $value);

        return $this;
    }

    /**
     * Get variable from data
     *
     * @param   string  $key
     *
     * @return  mixed
     */
    public function __get ($key)
    {
        return $this->data->get($key);
    }
}
