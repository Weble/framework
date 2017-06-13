<?php

namespace Zoolanders\Framework\Event\Type;

class Coreconfig extends Type
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Coreconfig constructor.
     * @param \Type $type
     * @param array $config
     */
    public function __construct(\Type $type)
    {
        parent::__construct($type);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->value;
    }

    public function setConfig($config)
    {
        $this->value = $config;
    }
}
