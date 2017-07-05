<?php

namespace Zoolanders\Framework\Schema;


class Property
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean
     */
    public $required = false;

    /**
     * Property constructor.
     * @param string $name
     * @param Schema $definition
     */
    public function __construct ($name, Schema $definition)
    {
        $this->name = $name;
        $this->schema = $definition;

        $this->type = $this->schema->getType();
    }

    /**
     * @return array|bool|int|mixed|null|\stdClass|string
     */
    public function getDefaultValue ()
    {
        return $this->schema->default ? $this->schema->default : $this->getDefaultValueFromType();
    }

    /**
     * @return array|bool|int|null|\stdClass|string
     */
    public function getDefaultValueFromType ()
    {
        switch ($this->type) {
            case 'object':
                return new \stdClass();
            case 'array':
                return [];
            case 'boolean':
                return true;
            case 'null':
                return null;
            case 'number':
            case 'integer':
                return 0;
            case 'string':
                return '';
        }
    }

    public function isItems()
    {
        return $this->schema->getProperties('items') ? true : false;
    }

}