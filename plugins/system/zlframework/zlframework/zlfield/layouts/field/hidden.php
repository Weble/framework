<?php


defined('_JEXEC') or die();

	// init vars
	$attrs = '';
	$id = $params->get('id');

	$class = ($params->get('class') ? " {$params->get('class')}" : '');

	// attributes
	$attrs .= $params->get('type') ? " data-type='hidden'" : '';

?>

<div data-id="<?php echo $id ?>" data-layout="hidden" class="zl-row<?php echo $class ?>" <?php echo $attrs ?>>
	<?php echo $field ?>
</div>