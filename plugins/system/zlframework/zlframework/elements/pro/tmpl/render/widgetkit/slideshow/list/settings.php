<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{
		"slides_settings": {
			"type":"subfield",
			"path":"elements:pro\/tmpl\/render\/widgetkit\/slideshow\/slides_settings.php"
		},
		"navigation":{
			"type": "select",
			"label": "Navigation Width",
			"default": "200",
			"specific":{
				"options":{
					"100px":"100",
					"150px":"150",
					"200px":"200",
					"250px":"250"
				}
			}
		},
		"animated":{
			"type": "select",
			"label": "Effect",
			"default": "fade",
			"specific":{
				"options":{
					"Fade":"fade",
					"Scroll":"scroll"
				}
			}
		}
	}';