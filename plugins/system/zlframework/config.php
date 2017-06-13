<?php

defined('_JEXEC') or die('Restricted access');

// load zoo
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// init vars
$zoo = App::getInstance('zoo');
$path = JPATH_ROOT.'/plugins/system/zlframework/zlframework';
$media = JPATH_ROOT.'/media/com_zoolanders';
$cuselms = JPATH_ROOT.'/media/zoo/custom_elements';

// register paths
$zoo->path->register($path, 'zlfw');
$zoo->path->register($media, 'zlmedia');

$zoo->path->register($path.'/zlfield', 'zlfield');
$zoo->path->register($path.'/zlfield/fields/elements', 'zlfields'); // temporal until all ZL Extensions adapted
$zoo->path->register($path.'/zlfield/fields/elements', 'fields'); // necessary since ZOO 2.5.13

$zoo->path->register($path.'/elements', 'elements');
$zoo->path->register($cuselms, 'elements');

$zoo->path->register($path.'/helpers', 'helpers');
$zoo->path->register($path.'/models', 'models');
$zoo->path->register($path.'/controllers', 'controllers');
$zoo->path->register($path.'/classes', 'classes');

// register classes
$zoo->loader->register('ZLModel', 'models:zl.php');
$zoo->loader->register('ZLModelItem', 'models:item.php');
$zoo->loader->register('ElementPro', 'elements:pro/pro.php');
$zoo->loader->register('ElementRepeatablepro', 'elements:repeatablepro/repeatablepro.php');
$zoo->loader->register('ElementFilespro', 'elements:filespro/filespro.php');
$zoo->loader->register('zlHelper', 'helpers:zl.php'); // necesary because of ZLElements old helper, this one overrides it
$zoo->loader->register('ZLStorage', 'classes:zlstorage/ZLStorage.php');
$zoo->loader->register('ZlfieldHelper', 'zlfield:zlfield.php');
