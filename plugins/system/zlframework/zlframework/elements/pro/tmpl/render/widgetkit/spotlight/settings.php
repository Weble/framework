<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{"fields": {

		"spotlight_effect":{
			"type": "select",
			"label": "Effect",
			"specific":{
				"options":{
					"Default":"",
					"Top":"top",
					"Bottom":"bottom",
					"Left":"left",
					"Right":"right",
					"Fade":"fade"
				}
			}
		},
		"spotlight_duration":{
			"label": "Effect Duration (ms)",
			"type":"text",
			"specific":{
				"placeholder":"Default"
			}
		}
		'.(isset($params['captions']) ? ',
		"captions":{
			"type": "select",
			"label": "Captions",
			"default": "1",
			"specific":{
				"options":{
					"PLG_ZLFRAMEWORK_DEFAULT":"1",
					"PLG_ZLFRAMEWORK_CUSTOM":"2"
				}
			},
			"dependents":"custom_caption > 2"
		},
		"custom_caption":{
			"label":"Custom Caption",
			"type":"text"
		}' : '').'

	}}';