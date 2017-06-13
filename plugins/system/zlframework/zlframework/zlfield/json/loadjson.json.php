<?php


defined('_JEXEC') or die();

	// loads the json string from provided path
	return include($this->app->path->path($params->find('load.path')));
?>