<?php

namespace Zoolanders\Framework\Schema;

use League\JsonGuard\Validator;
use League\JsonReference\Dereferencer;
use Zoolanders\Framework\Data\Data;

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
     * @var array
     */
    protected $properties = [];

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
            $this->links = new Data($this->links);
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
     * @return Property[]
     */
    public function getProperties ()
    {
        if (!$this->isLoaded()) {
            return [];
        }

        if (!$this->properties) {
            $this->properties = [];

            if (!isset($this->schema->properties)) {
                return [];
            }

            $required = $this->getRequiredProperties();
            foreach ($this->schema->properties as $key => $schema) {
                $this->properties[$key] = new Property($key, new Schema($schema));

                if (in_array($key, $required)) {
                    $this->properties[$key]->required = true;
                }
            }
        }

        return $this->properties;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->schema->type;
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
        $href = $linkSchema->href;
        $method = $linkSchema->method;

        if (!isset($this->links[$href])) {
            $this->links[$href] = new Data();
        }

        $this->links[$href][$method] = new Link($linkSchema);
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

    /**
     * @param $data
     * @return Validator
     */
    public function validate($data)
    {
        $data = (object) $data;

        return new Validator($data, $this->getSchemaDefinition());
    }
}
