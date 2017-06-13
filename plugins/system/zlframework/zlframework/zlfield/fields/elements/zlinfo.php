<?php


defined('_JEXEC') or die();

	echo '<div data-element-type="'.$parent->element->getElementType().'" class="zlinfo"><small>'.$parent->element->identifier.'</small>'
		.'<span><small>'.$parent->element->getMetaData('name').' '.$parent->element->getMetaData('version')
		.' <a href="https://www.zoolanders.com/" target="_blank" title="ZOOlanders">by ZL</a></small></span></div>';