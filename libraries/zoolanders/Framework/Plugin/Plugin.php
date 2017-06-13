<?php

namespace Zoolanders\Framework\Plugin;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Zoo;

abstract class Plugin extends \JPlugin
{
    /**
     * @var Container
     */
    protected $container;

    protected $events = [];

    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        $this->container = \Zoolanders\Framework\Container\Container::getInstance();

        // load default and current language
        $this->container->language->load('plg_zoolanders_' . $this->_name, JPATH_ADMINISTRATOR, 'en-GB', true);
        $this->container->language->load('plg_zoolanders_' . $this->_name, JPATH_ADMINISTRATOR, null, true);

        $this->registerNamespace();
        $this->loadEvents();
    }

    protected function registerNamespace()
    {
        $name = ucfirst(strtolower($this->_name));
        $path = JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/' . $name;

        $this->container->registerExtension('zoolingual', $path);
    }

    protected function loadEvents()
    {
        $this->container->event->bindEvents($this->events);
    }
}