<?php


defined('_JEXEC') or die();

	// init vars
	$options = array();

	$groups = $this->app->zoo->getGroups();
	foreach ($groups as $group) {
		$options[JText::_($group->name)] = $group->id;
	};

	return
	'{
		"access":{
			"type": "select",
			"label": "'.$params->find('access.label').'",
			"help": "'.$params->find('access.help').'",
			"default":"'.$this->app->joomla->getDefaultAccess().'",
			"specific": {
				"options":'.json_encode($options).'
			}
		}
	}';
?>