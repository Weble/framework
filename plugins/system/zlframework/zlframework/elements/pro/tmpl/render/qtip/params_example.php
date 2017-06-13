<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{"fields": {
		"layout_wrapper":{
			"type": "fieldset",
			"fields": {

				"layout_sep":{
					"type": "separator",
					"text": "PLG_ZLFRAMEWORK_QTIP_DISPLAY",
					"big": "1"
				},
				"qtip_options":{
					"type": "wrapper",
					"fields": {
						"_subfield": {
							"type": "subfield",
							"path":"zlfw:elements\/pro\/tmpl\/render\/qtip\/qtip_options.php"
						}

					}
				},

				"qtip_layout":{
					"type": "wrapper",
					"fields": {
						"sep-specific":{
							"type": "separator",
							"text": "PLG_ZLFRAMEWORK_QTIP_LAYOUT",
							"big":"true"
						},

						"sep_filter":{
							"type": "separator",
							"text": "PLG_ZLFRAMEWORK_FILTER"
						},
						"sub_filter": {
							"type": "subfield",
							"path":"zlfield:json\/filter.json.php",
							"control":"filter"
						},

						"sep_separator":{
							"type": "separator",
							"text": "PLG_ZLFRAMEWORK_SP_SEPARATOR"
						},
						"sub_separator": {
							"type": "subfield",
							"path":"zlfield:json\/separator.json.php",
							"control":"separator"
						}
					}
				}

			}
		}
	},
	"control": "qtip"}';
