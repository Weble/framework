<?php


defined('_JEXEC') or die();

	return
	'{
		"_by":{
			"type":"separatedby",
			"label":"PLG_ZLFRAMEWORK_SP_BY",
			"help":"PLG_ZLFRAMEWORK_SP_BY_DESC",
			"specific":{
				"repeatable":"'.$element->config->get('repeatable').'"
			},
			"default":"separator=[ ]",
			"dependents":"_by_custom > custom"
		},
		"_by_custom":{
			"type":"textarea",
			"label":"PLG_ZLFRAMEWORK_SP_BY_CUSTOM",
			"help":"PLG_ZLFRAMEWORK_SP_BY_CUSTOM_DESC"
		},
		"_class":{
			"type":"text",
			"label":"PLG_ZLFRAMEWORK_SP_CLASS",
			"help":"PLG_ZLFRAMEWORK_SP_CLASS_DESC"
		},
		"_fixhtml":{
			"type":"checkbox",
			"label":"PLG_ZLFRAMEWORK_SP_FIX_HTML",
			"help":"PLG_ZLFRAMEWORK_SP_FIX_HTML_DESC",
			"default":"0",
			"specific":{
				"label":"Yes"
			},
			"dependents": "_fixhtml_warning > 1"
		},
		"_fixhtml_warning":{
			"type":"info",
			"specific":{
				"text":"PLG_ZLFRAMEWORK_SP_FIX_HTML_WARNING_DESC"
			}
		}
	}';