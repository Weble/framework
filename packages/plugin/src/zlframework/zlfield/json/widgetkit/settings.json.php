<?php

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

// init vars
$type = $psv->get('_widget');
$style = $psv->get('style', 'default');

// load WK settings
$widget_xml_file = $this->app->path->path("media:widgetkit/widgets/$type/$type.xml");
$style_xml_file = $this->app->path->path("media:widgetkit/widgets/$type/styles/$style/config.xml");

if (!$widget_xml_file || !$style_xml_file) {
    return '{
            "fields": {
                "_info":{
                    "type":"info",
                    "specific":{
                        "text":"PLG_ZLFRAMEWORK_NOT_SUPPORTED_WIDGET"
                    }
                }
            }
        }';
}

$widget_xml = simplexml_load_file($widget_xml_file);
$style_xml = simplexml_load_file($style_xml_file);

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
