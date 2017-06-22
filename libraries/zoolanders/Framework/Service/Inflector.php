<?php

namespace Zoolanders\Framework\Service;

class Inflector {
    /**
     * @var \Joomla\String\Inflector
     */
    protected $inflector;

    /**
     * Constructor
     */
    public function __construct () {
        $this->inflector = \Joomla\String\Inflector::getInstance();
    }

    /**
     * @param $word
     * @return mixed
     */
    public function camelize ($word) {
        $word = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $word);
        $word = str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $word))));

        return $word;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call ($name, $arguments) {
        return call_user_func_array([$this->inflector, $name], $arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get ($name) {
        return $this->inflector->$name;
    }
}
