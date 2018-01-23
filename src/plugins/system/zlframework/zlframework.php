<?php

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

    function onAfterInitialise ()
    {
        if (!$this->checkDependencies()) {
            return;
        }

        require_once JPATH_LIBRARIES . '/zoolanders/include.php';

        $this->container = \Zoolanders\Framework\Container\Container::getInstance();
        $this->app = $this->container->zoo->getApp();

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

    /**
     * @param bool $event
     */
    public function onBeforeRender ($event = false)
    {
        if (!$this->checkDependencies()) {
            return;
        }

        // First time it's called by the joomla dispatcher
        if (!$event) {
            // trigger a Environment/Init event
            $event = $this->container->event->create('Environment\BeforeRender');
            $this->container->event->trigger($event);
        }
    }

    function checkDependencies ()
    {
        if (!JFile::exists(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php')
            || !JComponentHelper::getComponent('com_zoo', true)->enabled
        ) {
            return false;
        }

        if (!JFile::exists(JPATH_ROOT . '/libraries/zoolanders/include.php')) {
            return false;
        }
    }

}
