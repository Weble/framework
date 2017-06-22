<?php

defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	// init vars
	$type = $psv->get('_widget');
	$style = $psv->get('style', 'default');

	// load WK settings
	$widget_xml = simplexml_load_file($this->app->path->path("media:widgetkit/widgets/$type/$type.xml"));
	$style_xml = simplexml_load_file($this->app->path->path("media:widgetkit/widgets/$type/styles/$style/config.xml"));

	return
	'{"fields": {

		"widget_separator":{
			"type":"separator",
			"text":"Settings",
			"layout":"subsection"
		},
		"_widget_settings": {
			"type":"wrapper",
			"fields": {' . $this->app->zlfw->widgetkit->fromSettingsToZLfield($widget_xml->xpath('settings/setting')) . '}
		},
		"_widget_style_settings": {
			"type":"wrapper",
			"fields": {' . $this->app->zlfw->widgetkit->fromSettingsToZLfield($style_xml->xpath('settings/setting')) . '}
		}

	}}';