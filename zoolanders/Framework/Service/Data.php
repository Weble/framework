<?php

namespace Zoolanders\Framework\Service;

class Data
{
    /**
     * Create a data object
     *
     * @param mixed $data The data to load
     * @param string $format The data format (default: json)
     *
     * @return Data The class representing the data
     *
     * @since 1.0.0
     */
    public function create($data = array(), $format = 'json') {
        $class = '\\Zoolanders\\Framework\\Data\\' . ucfirst(strtolower($format));
        return new $class($data);
    }
}
