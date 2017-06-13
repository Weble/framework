<?php

namespace Zoolanders\Framework\Service;

class Dependencies
{
    /**
     * Dependencies constructor.
     * @param Path $path
     */
    public function __construct(Path $path, Filesystem $fs, Zoo $zoo)
    {
        $this->path = $path;
        $this->filesystem = $fs;
        $this->zoo = $zoo;
    }

    /**
     * Checks if ZOO extensions meet the required version
     *
     * @param   string $file The file with the dependencies to check
     * @return boolean true if all requirements are met
     */
    public function check($file)
    {
        // init vars
        $status = array('state' => true, 'extensions' => array());
        $groups = $this->path->path($file);

        // get the content from file
        if ($groups && $groups = json_decode($this->filesystem->read($groups))) {
            // iterate over the groups
            foreach ($groups as $group => $dependencies) foreach ($dependencies as $name => $dependency) {
                if ($group == 'plugins') {
                    // get plugin
                    $folder = isset($dependency->folder) ? $dependency->folder : 'system';
                    $plugin = \JPluginHelper::getPlugin($folder, strtolower($name));

                    // if plugin disable, skip it
                    if (empty($plugin)) continue;
                } elseif ($group == 'elements') {

                    // get plugin
                    $folder = isset($dependency->folder) ? $dependency->folder : 'system';
                    $plugin = \JPluginHelper::getPlugin($folder, 'zoo_zlelements');

                    // if plugin disable, skip it
                    if (empty($plugin)) continue;
                }

                $version = $dependency->version;
                $manifest = $this->path->path('root:' . $dependency->manifest);

                if ($version && $this->filesystem->has($manifest) && $xml = simplexml_load_string($this->filesystem->read($manifest))) {

                    // check if the extension is outdated
                    if (version_compare($version, (string)$xml->version, 'g')) {
                        $status['state'] = false;
                        $status['extensions'][] = array('dependency' => $dependency, 'installed' => $xml);
                    }

                }
            }
        }

        return $status;
    }

    /**
     * Warn about outdated extensions
     * @param array $extensions The list of estensions to be warned about
     * @param string $extension The extension triggering the warning
     */
    public function warn($extensions, $extension = 'ZL Framework')
    {
        foreach ($extensions as $ext) {
            $dep_req = $ext['dependency']; // required
            $dep_inst = $ext['installed']; // installed

            // set name
            $name = isset($dep_req->url) ? "<a href=\"{$dep_req->url}\" target=\"_blank\">{$dep_inst->name}</a>" : (string)$dep_inst->name;

            // set message
            $message = isset($dep_req->message) ? \JText::sprintf((string)$dep_req->message, $extension, $name) : \JText::sprintf('PLG_ZLFRAMEWORK_UPDATE_EXTENSION', $extension, $name);

            // raise notice
            $this->zoo->getApp()->error->raiseNotice(0, $message);
        }

    }

}
