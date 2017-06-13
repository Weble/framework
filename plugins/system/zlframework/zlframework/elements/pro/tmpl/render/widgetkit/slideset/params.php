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
				"sep_slides":{
					"type": "separator",
					"text": "Slides"
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
				"effect":{
					"type": "select",
					"label": "Effect",
					"default": "slide",
					"specific": {
						"options": {
							"Slide":"slide",
							"Zoom":"zoom",
							"Deck":"deck"
						}
					}
				},
				"index":{
					"type": "text",
					"label": "Start Index",
					"default": "0"
				},
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
				"style_settings": {
					"type":"subfield",
					"path":"elements:pro\/tmpl\/render\/widgetkit\/slideset\/{value}\/settings.php"
				},
				"specific_settings": {
					"type":"subfield",
					"path":"elements:{element}\/params\/widgetkit\/slideset\/{value}.php"
				}
			}
		}
	},
	"control": "settings"}';