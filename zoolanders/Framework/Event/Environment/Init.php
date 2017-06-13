<?php

namespace Zoolanders\Framework\Event\Environment;

use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Service\System\Application;

class Init extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var string
     */
    protected $side;

    /**
     * @var string
     */
    protected $component;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $task;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Init constructor.
     *
     */
    public function __construct(Request $request, Application $application)
    {
        $this->request = $request;
        $this->application = $application;

        $this->side = $this->application->isAdmin() ? 'admin' : 'site';
        $this->component = str_replace('com_', '', $request->getCmd('option', ''));
        $this->controller = $request->getCmd('controller', false);
        $this->view = $request->getCmd('view', false);
        $this->task = $request->getCmd('task', false);
    }

    /**
     * @param $env
     * @return bool
     */
    public function is($env)
    {
        return (strpos($this->get(), $env) === 0);
    }

    /**
     * get The Enviroment
     *
     * @return @string An known enviroment in simple string
     *
     * @since 3.0.6
     */
    public function get()
    {
        // ZOO
        if ($this->component == 'zoo') {
            $path = 'zoo-';
            switch ($this->task) {
                case 'editelements':
                case 'addelement':
                    $path .= 'type-edit';
                    break;

                case 'assignelements':
                    $path .= 'type-assignment';
                    break;

                case 'assignsubmission':
                    $path .= 'type-assignment-submission';
                    break;

                case 'edit':
                    $path .= 'item-edit';
                    break;

                case 'add':
                    if ($this->controller == 'new' && strlen($this->group)) $path .= 'app-config';
                    break;

                default:
                    if ($this->controller == 'configuration') $path .= 'app-config';
                    break;
            }

            return $path;
        }

        // Modules
        if ($this->component == 'advancedmodules' || $this->component == 'modules') {
            return 'joomla-module';
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return ($this->getSide() == 'admin');
    }

    /**
     * @return bool
     */
    public function isSite()
    {
        return !$this->isAdmin();
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getSide()
    {
        return $this->side;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }
}
