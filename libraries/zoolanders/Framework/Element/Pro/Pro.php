<?php

namespace Zoolanders\Framework\Element\Pro;

use Zoolanders\Framework\Container\Container;

/*
	Class: ElementPro
		The Element Pro abstract class
*/

abstract class Pro extends \Element
{
    /**
     * @var Container
     */
    protected $container;

    /*
       Function: Constructor
    */
    public function __construct ()
    {
        // call parent constructor
        parent::__construct();

        $this->container = Container::getInstance();

        // set callbacks
        $this->registerCallback('returndata');

        // load default and current language
        $this->app->system->language->load('plg_system_zoo_zlelements_' . $this->getElementType(), JPATH_ADMINISTRATOR, 'en-GB');
        $this->app->system->language->load('plg_system_zoo_zlelements_' . $this->getElementType(), JPATH_ADMINISTRATOR);
    }

    /*
        Function: setType
            Set related type object.
             Added a checkInstallation call to allow for extra steps of checkin installation
             on advanced elements. Here and not in the constructor to be sure to have type and
             therefore config available

        Parameters:
            $type - type object

        Returns:
            Void
    */
    public function setType ($type)
    {
        parent::setType($type);

        $this->checkInstallation();
    }

    /*
        Function: checkInstallation
            Allow for extra steps of checkin installation
             on advanced elements.

        Returns:
            Void
    */
    protected function checkInstallation ()
    {

    }

    /*
        Function: getLayout
            Get element layout path and use override if exists.

        Returns:
            String - Layout path
    */
    public function getLayout ($layout = null)
    {

        // init vars
        $type = $this->getElementType();

        // set default
        if ($layout == null) {
            $layout = "default.php";
        }

        // find layout
        if ($path = $this->app->path->path("elements:{$type}/tmpl/{$layout}")) {
            return $path;
        }

        // if layout not found, search on pro element
        return $this->app->path->path("elements:pro/tmpl/{$layout}");
    }

    /*
        Function: returnData
            Renders the element data - use for ajax requests
    */
    public function returnData ($layout, $separator = '', $filter = '', $specific = '')
    {
        $layout = json_decode($layout, true);
        $separator = json_decode($separator, true);
        $filter = json_decode($filter, true);
        $specific = json_decode($specific, true);
        $params = compact('layout', 'separator', 'filter', 'specific');
        return $this->render($params);
    }

    /*
        Function: render
            Renders the element.

       Parameters:
            $params - AppData render parameter

        Returns:
            String - html
    */
    public function render ($params = array())
    {
        $params = $this->app->data->create($params);

        // render layout
        if ($layout = $this->getLayout('render/' . $params->find('layout._layout', 'default.php'))) {
            return $this->renderLayout($layout, compact('params'));
        }
    }

}
