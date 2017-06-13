<?php


defined('_JEXEC') or die();

// init vars
$attrs = '';

echo '<textarea '.$attrs.' name="'.$name.'" >'.$this->values->get($id).'</textarea>';