<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{
		"autoplay":{
			"type": "radio",
			"label": "Autoplay",
			"default": "1"
		},
		"interval":{
			"type": "text",
			"label": "Autoplay Interval (ms)",
			"default": "5000"
		},
		"width":{
			"type": "text",
			"label": "Width",
			"default": "auto"
		},
		"height":{
			"type": "text",
			"label": "Height",
			"default": "auto"
		},
		"duration":{
			"type": "text",
			"label": "Effect Duration (ms)",
			"default": "500"
		},
		"index":{
			"type": "text",
			"label": "Start Index",
			"default": "0"
		},
		"order":{
			"type": "select",
			"label": "Order",
			"default": "default",
			"specific": {
				"options": {
					"Default":"default",
					"Random":"random"
				}
			}
		}
	}';