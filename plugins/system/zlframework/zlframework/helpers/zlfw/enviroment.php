<?php


defined('_JEXEC') or die();

App::getInstance('zoo')->loader->register('zlfwHelperEnvironment', 'helpers:zlfw/environment.php');

// workaround for outdated extensions calling this helper instead of the new one
class zlfwHelperEnviroment extends zlfwHelperEnvironment {}