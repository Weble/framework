<?php


defined('_JEXEC') or die('Restricted access');

// load assets
$this->app->document->addStylesheet('elements:separator/tmpl/edit/section/style.css');
$this->app->document->addScript('elements:separator/tmpl/edit/section/script.min.js');
$this->app->zlfw->zlux->loadBootstrap();

// init vars
$title 			= $this->config->get('name', '');
$folding 		= $this->config->find('layout._folding', '');

?>

<div id="<?php echo $this->identifier; ?>">

	<script type="text/javascript">
		jQuery(function($) {
			$("#<?php echo $this->identifier; ?>").ZOOtoolsSeparatorSection({
				title: '<?php echo $title; ?>',
				folding: '<?php echo $folding ?>'
			});
		});
	</script>

</div>
