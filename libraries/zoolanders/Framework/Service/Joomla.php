<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Service\System\Application;
use Zoolanders\Framework\Service\System\Config;

class Joomla
{
    /**
     * The current joomla version
     *
     * @var \JVersion
     * @since 1.0.0
     */
    public $version;

    /**
     * Class Constructor
     *
     * @param App $app A reference to the global app object
     */
    public function __construct (Config $config, Application $application, \JVersion $version)
    {
        \JLoader::import('joomla.version');

        $this->config = $config;
        $this->application = $application;
        $this->version = $version;
    }

    /**
     * Get the current Joomla installation short version (i.e: 2.5.3)
     *
     * @return string The short version of joomla (ie: 2.5.3)
     *
     * @since 1.0.0
     */
    public function getVersion ()
    {
        return $this->version->getShortVersion();
    }

    /**
     * Check the current version of Joomla
     *
     * @param string $version The version to check
     * @param boolean $release Compare only release versions (2.5 vs 2.5 even if 2.5.6 != 2.5.3)
     *
     * @return boolean If the version of Joomla is equal of the one passed
     *
     * @since 1.0.0
     */
    public function isVersion ($version, $release = true)
    {
        return $release ? $this->version->RELEASE == $version : $this->getVersion() == $version;
    }

    /**
     * Get the default access group
     *
     * @return int The default group id
     *
     * @since 1.0.0
     */
    public function getDefaultAccess ()
    {
        return $this->config->get('access');
    }

    /**
     * @return \JMenu
     */
    public function getMenu ()
    {
        return $this->application->getMenu('site');
    }
}
