<?php


defined('_JEXEC') or die();

// init vars
$value = $fld->find('settings.value');

echo '<input type="hidden" name="'.$name.'" value="'.$value.'" />';