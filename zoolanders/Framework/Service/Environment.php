<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Service\System\Application;

class Environment
{
    /**
     * @var
     */
    public $params;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $extension;

    /**
     * Environment constructor.
     */
    public function __construct(Request $input, Application $application, Zoo $zoo)
    {
        // set params as DATA class
        $this->zoo = $zoo;
        $this->params = $zoo->getApp()->data->create(array());
        $this->input = $input;
        $this->application = $application;
    }

    /**
     *
     * returns the current environment
     * Example environments:
     * Item View - site.com_zoo.item
     * Category View - site.com_zoo.category
     * ZOO manager - admin.com_zoo.manager
     *
     * @return string
     */
    public function get()
    {
        if (!$this->environment) {
            // init vars
            $environment = array();
            $jinput = $this->input;

            $component = $jinput->getCmd('option', null);
            $task = $jinput->getCmd('task', null);
            $view = $jinput->getCmd('view', null);

            // set back or frontend
            $environment[] = $this->application->isAdmin() ? 'admin' : 'site';

            // set component
            $environment[] = $component;

            // set controller
            $environment[] = $this->input->getCmd('controller', null);

            // if ZOO
            if ($component == 'com_zoo') {
                // if zoo item full view
                if ($task == 'item') {
                    $environment[] = 'item';
                    $this->params->set('item_id', $this->input->getCmd('item_id'));
                    unset($task);
                } else if ($view == 'item') { // if joomla item menu route
                    $environment[] = 'item';

                    if ($item_id = $this->input->getInt('item_id')) {
                        $this->params->set('item_id', $item_id);
                    } elseif ($menu = $this->zoo->getApp()->menu->getActive()) {
                        $this->params->set('item_id', $menu->params->get('item_id'));
                    }

                    unset($view);
                } // if zoo cat
                else if ($task == 'category') {
                    $environment[] = 'category';
                    $this->params->set('category_id', $this->input->getCmd('category_id'));
                    unset($task);
                } else if ($view == 'category') { // if joomla item menu route
                    $environment[] = 'category';

                    if ($menu = $this->container->zoo->getApp()->menu->getActive()) {
                        $this->params->set('category_id', $menu->params->get('category'));
                    }
                    unset($view);
                }
            }

            // add task/view to the environment
            if (isset($task) && !empty($task)) $environment[] = $task;
            else if (isset($view) && !empty($view)) $environment[] = $view;

            // clean values
            $environment = array_filter($environment);

            // return result in point format
            $this->environment = implode('.', $environment);
        }

        return $this->environment;
    }

    /**
     * Checks if the passed environment is the current environment
     *
     * @param $environments        string|array    array of or string separated by space of environments to check
     * @return boolean
     */
    public function is($environments = [])
    {
        if (!is_array($environments)) {
            // multiple environments posible
            $environments = explode(' ', $environments);
        }

        foreach ($environments as $env) {
            // if in any environment, return true
            if (strpos($this->get(), trim($env)) === 0) {
                return true;
                break;
            }
        }

        return false;
    }

    /**
     * Get current extension name
     *
     * @return mixed|string
     */
    public function currentExtension(){

        if(empty($this->extension)){
            $jinput = $this->input;
            $this->extension = $jinput->getCmd('option', $jinput->getCmd('plugin', false));
            if(preg_match('/^\w{1,3}_/', $this->extension)){
                $this->extension = preg_replace('/^\w{1,3}_/', '', $this->extension);
            }
        }

        return $this->extension;
    }
}
