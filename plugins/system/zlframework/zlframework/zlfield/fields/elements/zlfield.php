<?php


defined('_JEXEC') or die();

	// init var
	$node_atr = (array)$node->attributes();
	$node_atr = $node_atr['@attributes'];

	// set arguments
	$ajaxargs  = array('node' => $node_atr);
	$arguments = array('node' => $node_atr);
	$class	   = $node->attributes()->class;

	// if in element params, set it's arguments
	if(isset($parent->element) && $element = $parent->element){
		$ajaxargs['element_type'] = $element->getElementType();
		$ajaxargs['element_id']   = $element->identifier;

		$arguments['element'] = $element;
	}

	// parse fields
	$fields = $this->app->zlfield->parseArray($this->app->zlfw->xml->XMLtoArray($node), false, $arguments);

	// set json
	$json = '{"fields": {'.implode(",", $fields).'}}';

	// set ctrl
	$ctrl = "{$control_name}".($node->attributes()->addctrl ? "[{$node->attributes()->addctrl}]" : '');

	// set toggle hidden label
	$thl = $node->attributes()->togglelabel ? $node->attributes()->togglelabel : $node->attributes()->label;

	// render
	echo $this->app->zlfield->render(array($json, $ctrl, array(), '', false, $arguments), $node->attributes()->toggle, JText::_($thl), $ajaxargs, $class);
