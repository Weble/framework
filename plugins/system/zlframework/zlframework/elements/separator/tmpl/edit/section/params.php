<?php


defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{"fields": {

		"layout_wrapper":{
			"type":"fieldset",
			"fields": {

				"name":{
					"type":"text",
					"label":"PLG_ZOOTOLS_SEPARATOR_TITLE",
					"help":"PLG_ZOOTOLS_SEPARATOR_TITLE_DESC",
					"adjust_ctrl":{
						"pattern":'.json_encode('/\[layout\]/').',
						"replacement":""
					}
				},
				"_folding":{
					"type":"select",
					"label":"PLG_ZOOTOLS_SEPARATOR_FOLD_FEATURE",
					"help":"PLG_ZOOTOLS_SEPARATOR_FOLD_FEATURE_DESC",
					"specific": {
						"options": {
							"PLG_ZLFRAMEWORK_DISABLED":"",
							"PLG_ZOOTOLS_SEPARATOR_START_UNFOLDED":"1",
							"PLG_ZOOTOLS_SEPARATOR_START_FOLDED":"2"
						}
					}
				}

			}
		}

	}}';
