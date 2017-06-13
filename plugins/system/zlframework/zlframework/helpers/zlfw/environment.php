<?php


defined('_JEXEC') or die();

/*
	Class: zlfwHelperEnvironment
		The ZLFW environment helper class
*/
class zlfwHelperEnvironment extends AppHelper {

	public $params;

	/*
	   Function: Constructor
	*/
	public function __construct($app) {

		// call parent constructor
		parent::__construct($app);

		// set params as DATA class
		$this->params = $this->app->data->create(array());
	}

	/*
		Function: get
			returns the current environment

		Example environments:
			Item View - site.com_zoo.item
			Category View - site.com_zoo.category
			ZOO manager - admin.com_zoo.manager

		Returns:
			string - the environment route
	*/
	public function get()
	{
		// init vars
		$environment = array();
		$jinput      = JFactory::getApplication()->input;

		$Itemid = $jinput->getCmd('Itemid', null);
		$component = $jinput->getCmd('option', null);
		$task = $jinput->getCmd('task', null);
		$view = $jinput->getCmd('view', null);

		// set back or frontend
		$environment[] = $this->app->system->application->isAdmin() ? 'admin' : 'site';

		// set component
		$environment[] = $component;

		// set controller
		$environment[] = $this->app->request->getCmd('controller', null);

		// if ZOO
		if ($component == 'com_zoo'){

			// if zoo item full view
			if ($task == 'item') {
				$environment[] = 'item';
				$this->params->set('item_id', $this->app->request->getCmd('item_id'));
				unset($task);
			} else if ($view == 'item') { // if joomla item menu route
				$environment[] = 'item';

				if ($item_id = $this->app->request->getInt('item_id')) {
					$this->params->set('item_id', $item_id);
				} elseif ($menu = $this->app->menu->getActive()) {
					$this->params->set('item_id', $menu->params->get('item_id'));
				}

				unset($view);
			}

			// if zoo cat
			else if ($task == 'category') {
				$environment[] = 'category';
				$this->params->set('category_id', $this->app->request->getCmd('category_id'));
				unset($task);
			} else if ($view == 'category') { // if joomla item menu route
				$environment[] = 'category';

				if ($menu = $this->app->menu->getActive())
					$this->params->set('category_id', $menu->params->get('category'));
				unset($view);
			}
		}

		// add task/view to the environment
		if (isset($task) && !empty($task)) $environment[] = $task;
		else if (isset($view) && !empty($view)) $environment[] = $view;

		// clean values
		$environment = array_filter($environment);

		// return result in point format
		return implode('.', $environment);
	}

	/*
		Function: is
			checks if the passed environment is the current environment

		Params:
			@environments	string of environments to check separated by space

		Returns:
			boolean
	*/
	public function is($environments)
	{
		// multiple environments posible
		$environments = explode(' ', $environments);

		foreach ($environments as $env) {
			// if in any environment, return true
			if (strpos($this->get(), trim($env)) === 0) {
				return true;
				break;
			}
		}

		return false;
	}
}