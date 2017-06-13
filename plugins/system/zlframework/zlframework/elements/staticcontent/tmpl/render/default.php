<?php


defined('_JEXEC') or die('Restricted access');

	$separator = $params->find('separator._by_custom') != '' ? $params->find('separator._by_custom') : $params->find('separator._by');

	// render
	$result = $this->getRenderedValues($params);
	$result = $this->app->zlfw->applySeparators($separator, $result['result'], $params->find('separator._class'), $params->find('separator._fixhtml'));

	echo $this->app->zlfw->replaceShortCodes($result, array('item' => $this->_item));
?>
