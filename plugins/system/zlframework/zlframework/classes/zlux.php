<?php

defined('_JEXEC') or die();

/**
 * The ZLUX class
 */
class zlux {

	/**
	 * A reference to the global App object
	 *
	 * @var App
	 */
	public $app;

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 */
	public function __construct($app)
	{
		// set application
		$this->app = $app;
	}
}
