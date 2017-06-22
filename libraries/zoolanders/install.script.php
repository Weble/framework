<?php

defined('_JEXEC') or die();

/**
 * Heavily taken from FOF installer script (https://github.com/akeeba/fof)
 * Credits and copyright of FOF installer script are © 2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * Any changes to the original are copyrighted to © 2017 JOOlanders, SL
 */

// Do not declare the class if it's already defined. We have to put this check otherwise while updating
// multiple extensions at once will result in a fatal error since the class lib_zoolandersInstallerScript
// is already declared
if (class_exists('lib_zoolandersInstallerScript', false)) {
    return;
}

class lib_zoolandersInstallerScript {
    /**
     * The minimum PHP version required to install this extension
     *
     * @var   string
     */
    protected $minimumPHPVersion = '5.4.0';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     */
    protected $minimumJoomlaVersion = '3.4.0';

    /**
     * The maximum Joomla! version this extension can be installed on
     *
     * @var   string
     */
    protected $maximumJoomlaVersion = '3.9.99';

    /**
     * Joomla! pre-flight event. This runs before Joomla! installs or updates the component. This is our last chance to
     * tell Joomla! if it should abort the installation.
     *
     * @param   string $type Installation type (install, update, discover_install)
     * @param   JInstaller $parent Parent object
     *
     * @return  boolean  True to let the installation proceed, false to halt the installation
     */
    public function preflight ($type, $parent) {
        // Check the minimum PHP version
        if (!empty($this->minimumPHPVersion)) {
            if (defined('PHP_VERSION')) {
                $version = PHP_VERSION;
            } elseif (function_exists('phpversion')) {
                $version = phpversion();
            } else {
                $version = '5.0.0'; // all bets are off!
            }

            if (!version_compare($version, $this->minimumPHPVersion, 'ge')) {
                $msg = "<p>You need PHP $this->minimumPHPVersion or later to install this package but you are currently using PHP  $version</p>";

                JLog::add($msg, JLog::WARNING, 'jerror');

                return false;
            }
        }

        // Check the minimum Joomla! version
        if (!empty($this->minimumJoomlaVersion) && !version_compare(JVERSION, $this->minimumJoomlaVersion, 'ge')) {
            $jVersion = JVERSION;
            $msg = "<p>You need Joomla! $this->minimumJoomlaVersion or later to install this package but you only have $jVersion installed.</p>";

            JLog::add($msg, JLog::WARNING, 'jerror');

            return false;
        }

        // Check the maximum Joomla! version
        if (!empty($this->maximumJoomlaVersion) && !version_compare(JVERSION, $this->maximumJoomlaVersion, 'le')) {
            $jVersion = JVERSION;
            $msg = "<p>You need Joomla! $this->maximumJoomlaVersion or earlier to install this package but you have $jVersion installed</p>";

            JLog::add($msg, JLog::WARNING, 'jerror');

            return false;
        }

        // In case of an update, discovery etc I need to check if I am an update
        if (($type != 'install') && !$this->amIAnUpdate($parent)) {
            $msg = "<p>You have a newer version of ZOOlanders Framework installed. If you want to downgrade please uninstall it and install the older version.</p>";

            JLog::add($msg, JLog::WARNING, 'jerror');

            return false;
        }

        return true;
    }

    /**
     * Runs after install, update or discover_update. In other words, it executes after Joomla! has finished installing
     * or updating your component. This is the last chance you've got to perform any additional installations, clean-up,
     * database updates and similar housekeeping functions.
     *
     * @param   string $type install, update or discover_update
     * @param   JInstallerAdapterLibrary $parent Parent object
     */
    public function postflight ($type, JInstallerAdapterLibrary $parent) {
        $this->load();

        // Run any migration
        $manager = new \Zoolanders\Framework\Migration\Manager();
        $manager->run();
    }

    /**
     * Runs on uninstallation
     *
     * @param   JInstallerAdapterLibrary $parent The parent object
     *
     * @throws  RuntimeException  If the uninstallation is not allowed
     */
    public function uninstall (JInstallerAdapterLibrary $parent) {

    }


    /**
     * Is this package an update to the currently installed ZOOlanders Framework? If not (we're a downgrade) we will return false
     * and prevent the installation from going on.
     *
     * @param   JInstallerAdapterLibrary $parent The parent object
     *
     * @return  array  The installation status
     */
    protected function amIAnUpdate (JInstallerAdapterLibrary $parent) {
        /** @var JInstaller $grandpa */
        $grandpa = $parent->getParent();

        $target = JPATH_LIBRARIES . '/zoolanders';

        // If  is not really installed (someone removed the directory instead of uninstalling?) I have to install it.
        if (!JFolder::exists($target)) {
            return true;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions')->where('type LIKE ' . $db->q('library'))->where('element LIKE ' . $db->q('lib_zoolanders'));
        $db->setQuery($query);

        $extension = $db->loadObject();

        if (!$extension || !$extension->extension_id) {
            return true;
        }

        $installedVersionManifest = json_decode($extension->manifest_cache);
        if (!$installedVersionManifest || !$installedVersionManifest->version) {
            return true;
        }

        return version_compare($installedVersionManifest->version, (string)$parent->getManifest()->version, 'lte');
    }

    /**
     * Loads the framework
     */
    protected function load () {
        $filePath = JPATH_LIBRARIES . '/zoolanders/include.php';
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

}
