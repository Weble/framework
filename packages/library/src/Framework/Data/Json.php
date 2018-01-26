<?php

namespace Zoolanders\Framework\Data;

class Json extends Data
{
    /**
     * If the returned object will be an associative array (default :true)
     *
     * @var boolean
     */
    protected $assoc = true;

    /**
     * Class Constructor
     *
     * @param string|array $data The data to read. Could be either an array or a json string
     */
    public function __construct ($data = array())
    {
        // decode JSON string
        if (is_string($data)) {
            $data = json_decode($data, $this->assoc);
        }

        parent::__construct($data);
    }

    /**
     * Encode an array or an object in JSON format
     *
     * @param array|object $data The data to encode
     *
     * @return string The json encoded string
     */
    protected function write ($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
