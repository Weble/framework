<?php

// init vars
$attrs = '';
$preoptions = $fld->find('settings.options') ? $fld->find('settings.options') : array('JYES' => '1', 'JNO' => '0');
$options = array(); foreach ($preoptions as $text => $val) $options[] = $this->app->html->_('select.option', $val, $text);

echo $this->app->html->_('select.radiolist', $options, $name, $attrs, 'value', 'text', $this->values->get($id), $name, true);
