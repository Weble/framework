<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Service\System\Application;

class Installation
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var
     */
    protected $zoo;

    /**
     * @var
     */
    protected $dependencies;

    /**
     * @var
     */
    protected $environment;

    /**
     * @var Database
     */
    protected $db;

    /**
     * Installation constructor.
     * @param Application $application
     */
    public function __construct(
        Application $application,
        Environment $environment,
        Dependencies $dependencies,
        Zoo $zoo,
        Database $db
    )
    {
        $this->application = $application;
        $this->environment = $environment;
        $this->dependencies = $dependencies;
        $this->zoo = $zoo;
        $this->db = $db;
    }

    /**
     *  Check if the installation of the framework is ok
     */
    public function checkInstallation()
    {
        // if in admin views
        if ($this->application->isAdmin() && $this->environment->is('admin.com_zoo admin.com_installer admin.com_plugins')) {
            if ($this->checkDependencies()) {
                // let's define the check was succesfull to speed up other plugins loading
                if (!defined('ZLFW_DEPENDENCIES_CHECK_OK')) {
                    define('ZLFW_DEPENDENCIES_CHECK_OK', true);
                }

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     *  Check the Framework Dependencies
     */
    public function checkDependencies()
    {
        // prepare cache
        $cache = $this->zoo->getApp()->cache->create($this->zoo->getApp()->path->path('cache:') . '/zoolanders/framework', true, '86400', 'apc');

        // set plugins order
        $this->checkPluginOrder();

        // checks if dependencies are up to date
        $status = $this->dependencies->check("zlfw:dependencies.config");

        if (!$status['state']) {
            // warn but not if in installer to avoid install confusions
            if (!$this->environment->is('admin.com_installer')) {
                $this->dependencies->warn($status['extensions']);
            }
        }

        // save state to cache
        if ($cache && $cache->check()) {
            $cache->set('updated', $status['state']);
            $cache->save();
        }

        return $status['state'];
    }

    /**
     * Fix plugins order
     */
    public function checkPluginOrder($plugin = '')
    {
        // init vars
        $db = $this->db;
        $zf = $this->getPlugin('zlframework');

        $order = (int)$zf->ordering;

        // set ZOOlingual right after zlfw
        $order++;
        $db->setQuery("UPDATE `#__extensions` SET `ordering` = {$order} WHERE `type` = 'plugin' AND `element` = 'zoolingual'")->execute();

        // set ZOOtools and ZL Elements right after ZOOlingual
        $order++;
        $db->setQuery("UPDATE `#__extensions` SET `ordering` = {$order} WHERE `type` = 'plugin' AND `element` in
			('zootools', 'zoo_zlelements')
		")->execute();

        // set others and provided plugin after
        $order++;
        // known plugins
        $plugins = array('zooaccess', 'zooaksubs', 'zoocart', 'zoocompare', 'zoofilter', 'zooorder', 'zooseo', 'zootrack', 'zlwidgets');
        // add the new plugin
        if (!empty($plugin)) array_push($plugins, $plugin);
        // query
        $db->setQuery("UPDATE `#__extensions` SET `ordering` = {$order} WHERE `type` = 'plugin' AND `element` in ('" . implode('\',\'', $plugins) . "')")->execute();
    }

    /**
     * Retrieve an plugin object
     *
     * @param  string $name The plugin name
     * @param  string $type The plugin type
     *
     * @return Object The requested plugin
     */
    public function getPlugin($name, $type = 'system')
    {
        $db = $this->db;
        $query = 'SELECT * FROM #__extensions WHERE element LIKE ' . $db->Quote($name) . ' AND folder LIKE ' . $db->Quote($type) . ' LIMIT 1';

        $db->setQuery($query);
        return $db->loadObject();
    }
}
