<?php


defined('_JEXEC') or die();

	// init vars
	$attrs = '';
	$attrs .= $fld->get('dependent') ? " data-dependent='{$fld->get('dependent')}'" : '';

?>

	<div class="wrapper" data-id="<?php echo $id ?>" <?php echo $attrs ?>>
		<?php echo $content ?>
	</div>