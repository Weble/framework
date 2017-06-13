<?php


defined('_JEXEC') or die();

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

	// get zoo instance
	$zoo = App::getInstance('zoo');

	// init var
	$node_atr  = (array)$node->attributes();
	$node_atr  = $node_atr['@attributes'];
	$control   = $name;

	// set arguments
	$ajaxargs  = array('node' => $node_atr);
	$arguments = array('node' => $node_atr, 'addparams' => array('settings' => $value));

	// parse fields
	$fields = $zoo->zlfield->parseArray($zoo->zlfw->xml->XMLtoArray($node), false, $arguments);

	// set json
	$json = '{"fields": {'.implode(",", $fields).'}}';

	// set ctrl
	$ctrl = "{$control}".($node->attributes()->addctrl ? "[{$node->attributes()->addctrl}]" : '');

	// set toggle hidden label
	$thl = $node->attributes()->togglelabel ? $node->attributes()->togglelabel : $node->attributes()->label;

	// render
	echo $zoo->zlfield->render(array($json, $ctrl, array(), '', false, $arguments), $node->attributes()->toggle, JText::_($thl), $ajaxargs);