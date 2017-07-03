<?php

namespace Zoolanders\Framework\Schema;

class Link extends Schema
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $rel;

    /**
     * @var string
     */
    public $href;

    /**
     * @var string
     */
    public $method;

    /**
     * @var Schema
     */
    protected $targetSchema;

    /**
     * Link constructor.
     * @param array|null|object|string $linkDefinition
     */
    public function __construct ($linkDefinition)
    {
        $schema = null;
        if (isset($linkDefinition->schema)) {
            $schema = $linkDefinition->schema;
        }

        if (isset($linkDefinition->title)) {
            $this->title = $linkDefinition->title;
        }

        if (isset($linkDefinition->description)) {
            $this->description = $linkDefinition->description;
        }

        if (isset($linkDefinition->method)) {
            $this->method = $linkDefinition->method;
        }

        if (isset($linkDefinition->rel)) {
            $this->rel = $linkDefinition->rel;
        }

        if (isset($linkDefinition->href)) {
            $this->href = $linkDefinition->href;
        }

        parent::__construct($schema);

        if (isset($linkDefinition->targetSchema)) {
            $this->targetSchema = new Schema(($linkDefinition->targetSchema));
        }
    }

    /**
     * @return Schema
     */
    public function getTargetSchema()
    {
        return $this->targetSchema;
    }

}