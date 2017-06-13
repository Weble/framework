<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{

		"collapseall":{
			"type": "radio",
			"label": "Auto Collapse",
			"default": "1"
		},
		"matchheight":{
			"type": "radio",
			"label": "Match Content Height",
			"default": "1"
		},
		"index":{
			"type": "text",
			"label": "Start Index",
			"default": "0"
		},
		"duration":{
			"type": "text",
			"label": "Animation Speed (ms)",
			"default": "500"
		},
		"width":{
			"type": "text",
			"label": "Width",
			"default": "auto"
		},
		"order":{
			"type": "select",
			"label": "Order",
			"default": "default",
			"specific":{
				"options":{
					"Default":"default",
					"Random":"random"
				}
			}
		}

	}';