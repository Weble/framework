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
    public function __construct($data = array(), $code = 200)
    {
        $this->code = $code;
        $this->data = new Json($data);
    }

    /**
     * @inheritdoc
     */
    public function setContent($content){

        $this->data = new Json($content);
        return $this;
    }

    /**
     * Bind variable to data
     *
     * @param   string  Varname
     * @param   mixed   Value
     *
     * @return  object
     */
    public function __set($varname, $value)
    {
        if (null === $this->data) {
            $this->data = new Json();
        }

        $this->data->set($varname, $value);

        return $this;
    }

    /**
     * Get variable from data
     *
     * @param   string  Varname
     *
     * @return  mixed
     */
    public function __get($varname)
    {
        return $this->data->get($varname);
    }

    /**
     * Add a value to subarray (for example for errors)
     *
     * @param $varname
     * @param $value
     *
     * @return object
     */
    public function add($varname, $value)
    {
        $node = $this->{$varname};

        if (empty($node)) {
            $this->{$varname} = array();
            $node = array();
        }

        array_push($node, $value);

        $this->{$varname} = $node;

        return $this;
    }
}
