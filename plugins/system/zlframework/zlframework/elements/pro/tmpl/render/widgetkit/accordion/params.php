<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{"fields": {
		"wrapper-filter":{
			"type": "wrapper",
			"toggle": "Toggle Style Options",
			"fields": {
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
				"style_settings": {
					"type":"subfield",
					"path":"elements:pro\/tmpl\/render\/widgetkit\/accordion\/{value}\/settings.php"
				},
				"specific_settings": {
					"type":"subfield",
					"path":"elements:{element}\/params\/widgetkit\/accordion\/{value}.php"
				}
			}
		}
	},
	"control": "settings"}';