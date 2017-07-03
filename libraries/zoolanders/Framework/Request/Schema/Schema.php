<?php

namespace Zoolanders\Framework\Request\Schema;

use League\JsonReference\Dereferencer;

class Schema
{
    /**
     * @var object
     */
    protected $schema;

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @var Dereferencer
     */
    protected $dereferencer;

    /**
     * Schema constructor.
     * @param null|array|object|string $jsonFilePathOrSchemaObject
     */
    public function __construct ($jsonFilePathOrSchemaObject = null)
    {
        $this->dereferencer = new Dereferencer();

        $schema = null;

        if ($jsonFilePathOrSchemaObject && is_string($jsonFilePathOrSchemaObject) && file_exists($jsonFilePathOrSchemaObject)) {
            $schema = json_decode(file_get_contents($jsonFilePathOrSchemaObject));
        }

        if ($jsonFilePathOrSchemaObject && (is_object($jsonFilePathOrSchemaObject) || is_array($jsonFilePathOrSchemaObject))) {
            $schema = (object)$jsonFilePathOrSchemaObject;
        }

        if ($schema) {
            $this->loadSchema($schema);
        }
    }

    /**
     * @return bool
     */
    public function isLoaded ()
    {
        return (!empty($this->schema));
    }

    /**
     * @return object
     */
    public function getProperties ()
    {
        if (!$this->isLoaded()) {
            return new \stdClass();
        }

        if (!isset($this->schema->properties)) {
            return new \stdClass();
        }

        return $this->schema->properties;
    }

    /**
     * @return array
     */
    public function getRequiredProperties ()
    {
        if (!isset($this->schema->required)) {
            return [];
        }

        return $this->schema->required;
    }

    /**
     * @param $schema
     */
    public function loadSchema ($schema)
    {
        $this->schema = $this->dereferencer->dereference($schema);

        if ($this->isLoaded() && isset($this->schema->links)) {
            foreach ($this->schema->links as $link) {
                $this->loadLink($link);
            }
        }
    }

    /**
     * @param $linkSchema
     */
    public function loadLink ($linkSchema)
    {
        if (isset($linkSchema->rel)) {
            $this->links[$linkSchema->rel] = new Link($linkSchema);
        }
    }

    /**
     * @return object
     */
    public function getSchemaDefinition ()
    {
        return $this->schema;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param $rel
     * @return bool|Link
     */
    public function getLink($rel)
    {
        if (isset($this->links[$rel])) {
            return $this->links[$rel];
        }

        return false;
    }

    /**
     * Proxy the getter to the schema
     *
     * @param $name
     * @return mixed
     */
    public function __get ($name)
    {
        if (isset($this->schema->$name)) {
            return $this->schema->$name;
        }
    }
}