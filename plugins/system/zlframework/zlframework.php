<?php

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemZlframework extends JPlugin
{
    public $app;

    /**
     * @var \Zoolanders\Framework\Container\Container
     */
    protected $container;

    protected $autoloadLanguage = true;

    function onAfterInitialise()
    {
        require_once JPATH_LIBRARIES . '/zoolanders/include.php';

        $this->container = \Zoolanders\Framework\Container\Container::getInstance();
        $this->app = $this->container->zoo->getApp();

        // check and perform installation tasks
        if (!$this->container->installation->checkInstallation()) return; // must go after language, elements path and helpers

        // trigger a Environment/Init event
        $event = $this->container->event->create('Environment\Init');
        $this->container->event->trigger($event);

        // init ZOOmailing if installed
        if ($path = $this->app->path->path('root:plugins/acymailing/zoomailing/zoomailing')) {

            // register path and include
            $this->app->path->register($path, 'zoomailing');
            require_once($path . '/init.php');
        }

    }

    public function onBeforeRender()
    {
        // trigger a Environment/Init event
        $event = $this->container->event->create('Environment\BeforeRender');
        $this->container->event->trigger($event);
    }
}
