<?php

namespace Zoolanders\Framework\Service;

use App;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Table;

class Zoo
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * Zoo constructor.
     */
    public function __construct ()
    {
        // Autoload zoo
        $this->load();
    }

    public function addOverrides ()
    {
        $zooHelper = $this->app->table;
        $helper = new Table\Helper($this->app);
        $this->app->addHelper($helper, 'table');
    }

    /**
     * Proxy the call to the app itself
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call ($name, $arguments)
    {
        return call_user_func_array([$this->app, $name], $arguments);
    }

    /**
     * Proxy also the helpers calls
     * @param $name
     * @return mixed
     */
    public function __get ($name)
    {
        return $this->app->$name;
    }

    /**
     * Get the main zoo App class
     * @return App
     */
    public function getApp ()
    {
        return $this->app;
    }

    /**
     * Check if zoo is loaded
     * @return boolean
     */
    public function isLoaded ()
    {
        return $this->loaded;
    }

    /**
     * Load ZOO and its dependencies
     */
    public function load ()
    {
        if ($this->isLoaded()) {
            return;
        }

        $path = JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';

        // Zoo doesn't exists
        if (!file_exists($path)) {
            return;
        }

        // load zoo config
        require_once($path);

        // Something went wrong
        if (!class_exists('\\App')) {
            return;
        }

        // Component is disabled
        if (!\JComponentHelper::getComponent('com_zoo', true)->enabled) {
            return;
        }

        // init vars
        $this->app = App::getInstance('zoo');

        // Zoo version too old
        if (!version_compare($this->app->zoo->version(), '2.5', '>=')) {
            return;
        }

        $this->register();

        $this->loaded = true;
    }

    /**
     * Register all the needed stuff
     */
    protected function register ()
    {
        $this->registerPaths();
        $this->registerClasses();
        $this->addOverrides();
    }

    /**
     * Register the new paths within zoo
     */
    protected function registerPaths ()
    {
        // start up the framework stuff
        $path = JPATH_ROOT . '/plugins/system/zlframework/zlframework';
        $media = JPATH_ROOT . '/media/com_zoolanders';
        $cuselms = JPATH_ROOT . '/media/zoo/custom_elements';

        // register paths
        $this->app->path->register($path, 'zlfw');
        $this->app->path->register($media, 'zlmedia');

        $this->app->path->register($path . '/zlfield', 'zlfield');
        $this->app->path->register($path . '/zlfield/fields/elements', 'zlfields'); // temporal until all ZL Extensions adapted
        $this->app->path->register($path . '/zlfield/fields/elements', 'fields'); // necessary since ZOO 2.5.13

        $this->app->path->register($path . '/elements', 'elements');
        $this->app->path->register($cuselms, 'elements');

        $this->app->path->register($path . '/helpers', 'helpers');
        $this->app->path->register($path . '/models', 'models');
        $this->app->path->register($path . '/controllers', 'controllers');
        $this->app->path->register($path . '/classes', 'classes');

        // register classes
        $this->app->loader->register('ZLModel', 'models:zl.php');
        $this->app->loader->register('ZLModelItem', 'models:item.php');
        $this->app->loader->register('ElementPro', 'elements:pro/pro.php');
        $this->app->loader->register('ElementRepeatablepro', 'elements:repeatablepro/repeatablepro.php');
        $this->app->loader->register('ElementFilespro', 'elements:filespro/filespro.php');
        $this->app->loader->register('zlHelper', 'helpers:zl.php'); // necesary because of ZLElements old helper, this one overrides it
        $this->app->loader->register('ZLStorage', 'classes:zlstorage/ZLStorage.php');
        $this->app->loader->register('ZlfieldHelper', 'zlfield:zlfield.php');

        // register plugin path
        if ($path = $this->app->path->path('root:plugins/system/zoo_zlelements/zoo_zlelements')) {
            $this->app->path->register($path, 'zlpath');
        }

        // register elements path
        if ($path = $this->app->path->path('zlpath:elements')) {
            $this->app->path->register($path, 'elements');
        }

        // register fields path
        if ($path = $this->app->path->path('zlpath:fields')) {
            $this->app->path->register($path, 'zlfields');
        }

        // register plugin path
        if ($path = $this->app->path->path('root:plugins/system/zlframework/zlframework')) {
            $this->app->path->register($path, 'zlfw');
        }

        if ($path = JPATH_ROOT . '/media/zoo/custom_elements') {
            $this->app->path->register($path, 'elements'); // register custom elements path
        }
    }

    /**
     * Register the new classes within zoo
     */
    protected function registerClasses ()
    {
        // register helpers
        if ($path = $this->app->path->path('zlpath:helpers')) {
            $this->app->path->register($path, 'helpers');
            $this->app->loader->register('ZlHelper', 'helpers:zlhelper.php');
        }

        // register helpers
        if ($path = $this->app->path->path('zlfw:helpers')) {
            $this->app->path->register($path, 'helpers');
            $this->app->loader->register('zlfwHelper', 'helpers:zlfwhelper.php');
            $this->app->loader->register('ZLDependencyHelper', 'helpers:zldependency.php');
            $this->app->loader->register('ZlStringHelper', 'helpers:zlstring.php');
            $this->app->loader->register('ZlFilesystemHelper', 'helpers:zlfilesystem.php');
            $this->app->loader->register('ZlPathHelper', 'helpers:zlpath.php');
            $this->app->loader->register('ZlModelHelper', 'helpers:model.php');
            $this->app->loader->register('ZLXmlHelper', 'helpers:zlxmlhelper.php');
            $this->app->loader->register('ZLFieldHTMLHelper', 'helpers:zlfieldhtml.php');
        }

        // register classes
        if ($path = $this->app->path->path('zlfw:classes')) {
            $this->app->path->register($path, 'classes');
            $this->app->loader->register('ZLStorage', 'classes:zlstorage/ZLStorage.php');
        }

        // register elements fields
        if ($path = $this->app->path->path('zlfw:zlfield')) {
            $this->app->path->register($path, 'zlfield'); // used since ZLFW 2.5.8
            $this->app->path->register($path . '/fields/elements', 'zlfields'); // temporal until all ZL Extensions adapted
            $this->app->path->register($path . '/fields/elements', 'fields'); // necessary since ZOO 2.5.13
        }

        // register elements - order is important!
        if ($path = $this->app->path->path('zlfw:elements')) {
            $this->app->path->register($path, 'elements'); // register elements path

            $this->app->loader->register('ElementPro', 'elements:pro/pro.php');
            $this->app->loader->register('ElementRepeatablepro', 'elements:repeatablepro/repeatablepro.php');
            $this->app->loader->register('ElementFilespro', 'elements:filespro/filespro.php');
        }
    }
}
