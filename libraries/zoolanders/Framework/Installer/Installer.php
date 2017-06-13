<?php

namespace Zoolanders\Framework\Installer;

use Zoolanders\Framework\Container\Container;

defined('_JEXEC') or die();

abstract class Installer
{
    /**
     * @var Container
     */
    protected $container;

    /*
        Variable: source
            The install source folder.
    */
    public $source;

    /*
        Variable: target
            The install target folder.
    */
    public $target;

    /*
        Variable: type
            The install type
    */
    public $type;

    /*
        Variable: parent
            The parent object
    */
    public $parent;

    /*
        Variable: ext_name
            The extension name
    */
    public $ext_name;

    public $ext;

    public $lng_prefix;

    /*
        Variable: _ext_id
            The extension id
    */
    protected $ext_id;

    /*
        Variable: _error
            The error message
    */
    protected $error;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    /**
     * Set initials vars
     */
    public function initVars($type, $parent)
    {
        $this->type = strtolower($type);
        $this->parent = $parent;
        $this->source = $parent->getParent()->getPath('source');
        $this->target = $parent->getParent()->getPath('extension_root');
        $this->ext_name = (string)$parent->get('manifest')->name;
    }

    /**
     * Called before any type of action
     *
     * @param   string $type Which action is happening (install|uninstall|discover_install)
     * @param   object $parent The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Called on installation
     *
     * @param   object $parent The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function install($parent)
    {
    }

    /**
     * Called on update
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Called on uninstallation
     *
     * @param   object $parent The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function uninstall($parent)
    {
    }

    /**
     * Called after all actions
     *
     * @param   string $type Which action is happening (install|uninstall|discover_install)
     * @param   object $parent The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function postflight($type, $parent)
    {
        // init vars
        $new_version = (string)$parent->get('manifest')->version;

        // after install
        if ($type == 'install') {
            // set ext version
            $this->setVersion();

            // show
            echo \JText::sprintf('PLG_ZLFRAMEWORK_SYS_INSTALL', $this->ext_name, $new_version);
        }

        // after update
        if ($this->type == 'update') {
            // set ext version
            $this->setVersion();

            // show
            echo \JText::sprintf('PLG_ZLFRAMEWORK_SYS_UPDATE', $this->ext_name, $new_version);
        }

        // after uninstall
        if ($type == 'uninstall') {
            // remove version from schema table
            $this->cleanVersion();
        }
    }

    /**
     * Check dependencies
     *
     * @return  boolean  True on success
     */
    protected function checkDependencies($parent)
    {
        // init vars
        $dependencies = $parent->get("manifest")->dependencies->attributes();

        // check Joomla
        if ($min_v = (string)$dependencies->joomla) {
            // if up to date
            $joomla_release = $this->container->joomla->getVersion();

            if (version_compare((string)$joomla_release, $min_v, '<')) {
                $this->error = \JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_OUTDATED', $this->ext_name, 'http://www.joomla.org', 'Joomla!', $min_v);
                return false;
            }
        }

        // check ZOO
        if ($min_v = (string)$dependencies->zoo) {
            // if installed and enabled
            if (!$this->container->filesystem->has(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php')
                || !\JComponentHelper::getComponent('com_zoo', true)->enabled
            ) {
                $this->error = JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_MISSING', $this->ext_name, 'http://www.yootheme.com/zoo', 'ZOO');
                return false;
            }

            // if up to date
            $zoo_manifest = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_zoo/zoo.xml');

            if (version_compare((string)$zoo_manifest->version, $min_v, '<')) {
                $this->error = \JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_OUTDATED', $this->ext_name, 'http://www.yootheme.com/zoo', 'ZOO', $min_v);
                return false;
            }
        }

        // check ZL
        if ($min_v = (string)$dependencies->zl) {
            // if installed and enabled
            if (!$this->container->filesystem->has(JPATH_ADMINISTRATOR . '/components/com_zoolanders/zoolanders.php')
                || !\JComponentHelper::getComponent('com_zoolanders', true)->enabled
            ) {
                $this->error = \JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_MISSING', $this->ext_name, 'https://www.zoolanders.com/extensions/zoolanders', 'ZOOlanders Component');
                return false;
            }

            // if up to date
            $zl_manifest = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_zoolanders/zoolanders.xml');

            if (version_compare((string)$zl_manifest->version, $min_v, '<')) {
                $this->error = \JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_OUTDATED', $this->ext_name, 'https://www.zoolanders.com/extensions/zoolanders', 'ZOOlanders Component', $min_v);
                return false;
            }
        }

        // check ZLFW
        if ($min_v = (string)$dependencies->zlfw) {
            // if installed and enabled
            if (!\JPluginHelper::getPlugin('system', 'zlframework')) {
                $this->error = \JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_MISSING', $this->ext_name, 'https://www.zoolanders.com/extensions/zl-framework', 'ZL Framework');
                return false;
            }

            // if up to date
            $zlfw_manifest = simplexml_load_file(JPATH_ROOT . '/plugins/system/zlframework/zlframework.xml');

            if (version_compare((string)$zlfw_manifest->version, $min_v, '<')) {
                $this->error = \JText::sprintf('PLG_ZLFRAMEWORK_SYS_DEPENDENCY_OUTDATED', $this->ext_name, 'https://www.zoolanders.com/extensions/zl-framework', 'ZL Framework', $min_v);
                return false;
            }
        }

        return true;
    }

    /**
     * creates the lang string
     *
     * @return  string
     */
    protected function langString($string)
    {
        return $this->lng_prefix . $string;
    }

    /**
     * creates the lang string
     *
     * @return  string
     */
    protected function getExtID()
    {
        if (!$this->ext_id) {
            $this->container->db->setQuery("SELECT `extension_id` FROM `#__extensions` WHERE `element` = '{$this->ext}'");
            if ($plg = $this->container->db->loadObject())
                $this->ext_id = (int)$plg->extension_id;
        }

        return $this->ext_id;
    }

    /**
     * Gets the current version from schema table
     */
    public function getVersion()
    {
        // set query
        $this->container->db->setQuery("SELECT `version_id` FROM `#__schemas` WHERE `extension_id` = '{$this->getExtID()}'");

        // load and return
        if ($obj = $this->container->db->loadObject()) {
            return $obj->version_id;
        }

        return null;
    }

    /**
     * Sets the version in schemas table.
     *
     * @param string $version
     */
    public function setVersion($version = null)
    {
        // init vars
        $version = $version ? $version : (string)$this->parent->get('manifest')->version;
        $version = str_replace(array(' ', '_'), '', $version);
        $ext_id = $this->getExtID();

        // insert row
        $this->container->db->setQuery("SELECT * FROM `#__schemas` WHERE `extension_id` = '{$ext_id}'");
        if (!$this->container->db->loadObject()) {
            $query = $this->container->db->getQuery(true);
            $query->clear()
                ->insert($this->container->db->quoteName('#__schemas'))
                ->columns(array($this->container->db->quoteName('extension_id'), $this->container->db->quoteName('version_id')))
                ->values($ext_id . ', ' . $this->container->db->quote($version));
            $this->container->db->setQuery($query)->execute();

            // of update if exists
        } else {
            $query = $this->container->db->getQuery(true);
            $query->clear()
                ->update($this->container->db->quoteName('#__schemas'))
                ->set($this->container->db->quoteName('version_id') . ' = ' . $this->container->db->quote($version))
                ->where($this->container->db->quoteName('extension_id') . ' = ' . $ext_id);
            $this->container->db->setQuery($query)->execute();
        }
    }

    /**
     * Removes the version from schema table
     */
    protected function cleanVersion()
    {
        $this->container->db->setQuery("DELETE FROM `#__schemas` WHERE `extension_id` = '{$this->getExtID()}'")->execute();
    }

    /**
     * Return required update versions.
     *
     * @param string $version The version for which to get required updates
     * @param string $path The path where the updates are stored
     *
     * @return array versions of required updates
     */
    public function getRequiredUpdates($version, $path)
    {
        if ($files = \JFolder::files($path, '^\d+.*\.php$')) {
            $files = array_map(create_function('$file', 'return basename($file, ".php");'), array_filter($files, create_function('$file', 'return version_compare("' . $version . '", basename($file, ".php")) < 0;')));
            usort($files, create_function('$a, $b', 'return version_compare($a, $b);'));
        }

        return $files;
    }

    /**
     * Performs the next update.
     *
     * @param string $current_v The current version of the installed extension
     * @param string $path The path where the updates are stored
     *
     * @return bool Result of the update
     */
    public function runUpdates($current_v, $path)
    {
        // get required updates
        $updates = $this->getRequiredUpdates($current_v, $path);

        // run each of them
        foreach ($updates as $version) {
            if ((version_compare($version, $current_v) > 0)) {
                $class = '\\Update' . str_replace('.', '', $version);
                if (class_exists($class)) {

                    // make sure class implemnts zlUpdate interface
                    $r = new \ReflectionClass($class);
                    if ($r->isSubclassOf('\\Zoolanders\\Installer\\Updater') && !$r->isAbstract()) {

                        try {
                            // run the update
                            $r->newInstance()->run();
                        } catch (\Exception $e) {

                            \JError::raiseWarning(null, "Error during update! ($e)");
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}
