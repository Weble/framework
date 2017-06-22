<?php

defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{"fields": {

		"widget_separator":{
			"type": "separator",
			"text": "Accordion Widget",
			"big":"1"
		},
		"_style":{
			"type": "layout",
			"label": "Style",
			"default": "default",
			"specific": {
				"path":"elements:pro\/tmpl\/render\/widgetkit\/accordion",
				"mode":"folders"
			},
			"childs":{
				"loadfields": {

					"_style_settings": {
						"type":"subfield",
						"path":"elements:'.$element->getElementType().'\/tmpl\/render\/widgetkit\/accordion\/{value}\/params.php"
					}

				}
			}
		}

	}}';
