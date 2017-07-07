<?php

namespace Zoolanders\Framework\Migration;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class Manager
{
    /**
     * @var \Phinx\Migration\Manager
     */
    protected $manager;

    /**
     * Manager constructor.
     * @param array $config
     */
    public function __construct ($config = [])
    {
        $config = new Config($config);

        $input = new ArrayInput([]);
        $output = new NullOutput();

        $this->manager = new \Phinx\Migration\Manager($config, $input, $output);
    }

    /**
     * @param array $config
     */
    public function setConfig ($config = [])
    {
        $config = new Config($config);
        $this->manager->setConfig($config);
    }

    /**
     * public function getConfig()
     * {
     * return $this->manager->getConfig();
     * }
     *
     * /**
     * @param string $environment
     */
    public function run ($environment = 'production')
    {
        $this->manager->migrate($environment);
    }
}
