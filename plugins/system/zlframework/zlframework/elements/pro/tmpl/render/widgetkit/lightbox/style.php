<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return
	'{"fields": {

		"layout_settings": {'./* Params loaded from the Element layout */'
			"type":"subfield",
			"path":"elements:'.$element->getElementType().'\/tmpl\/render\/widgetkit\/lightbox\/params.php"
		}

	}}';