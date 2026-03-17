<?php 
// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', '1');
// require_once('data_tables.php');
$root_page = array_values(array_filter(preg_split('/\//',_PAGE_)));
?>
<?php  if ( in_array($root_page[0],array('index','country','country_print','world'))) : ?>
        <meta charset="UTF-8">
	<link rel="stylesheet" href="./css/main.min.css" media="screen">
	<link rel="stylesheet" href="./css/population_counter.min.css" media="screen">
	<link rel="stylesheet" href="./css/population_on_date.min.css" media="screen">
	<link rel="stylesheet" href="./css/population_growth.min.css" media="screen">
	<link rel="stylesheet" href="./css/population_pyramid.min.css" media="screen">
	<link rel="stylesheet" href="./css/populous_density.min.css" media="screen">
	<link rel="stylesheet" href="./css/world.min.css" media="all">
	<link rel="stylesheet" href="./css/world.print.min.css" media="print">


	<!-- external libraries -->
	<link rel="stylesheet" href="./js/jquery-ui.custom/css/custom-theme/jquery-ui.custom.min.css" media="screen">

<script src="./js/jquery-1.8.3.min.js" type="text/javascript"></script>
<!-- <script src="/main/jquery/latest-jquery/jquery.min.js" type="text/javasscript"></script> -->
<script src="./js/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="./js/jquery-ui-themes-1.10.0/themes/base/jquery-ui.css" type="text/javascript"></script>


	<script src="./js/raphael/raphael-min.js" type="text/javascript"></script>

	<!-- raphael.export required for svg-to-png -->
	<script src="./js/raphael/raphael.export.min.js" type="text/javascript"></script>

	<!-- g.raphael required for charting libraries -->
	<script src="./js/raphael/g.raphael.min.js" type="text/javascript"></script>
	<script src="./js/raphael/g.line.min.js" type="text/javascript" charset="utf-8"></script>

	<!-- custom objects -->
	<script src="./js/main.min.js" type="text/javascript"></script>
	<script src="./js/api.min.js" type="text/javascript"></script>
	<script src="./js/animator.min.js" type="text/javascript"></script>
	<script src="./js/counter.min.js" type="text/javascript"></script>
	<script src="./js/odometer.min.js" type="text/javascript"></script>
	<script src="./js/html_progress_bar.min.js" type="text/javascript"></script>
	<script src="./js/linechart_with_overlay.min.js" type="text/javascript"></script>
	<script src="./js/tabbed_table.min.js" type="text/javascript"></script>
	<script src="./js/population.js" type="text/javascript" charset="utf-8"></script>
	<script src="./js/slider.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="./js/pyramid_graph.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="./js/share.min.js" type="text/javascript"></script>

	<script src="<?php echo _APPROOT_;?>population_counters/population_counters_controller.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo _APPROOT_;?>population_on_date/population_on_date_controller.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo _APPROOT_;?>population_growth/population_growth_controller.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo _APPROOT_;?>population_pyramid/population_pyramid_controller.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo _APPROOT_;?>most_populous/most_populous_controller.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo _APPROOT_;?>highest_density/highest_density_controller.min.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript">
	var config = <?php echo json_encode($config); ?>;
	var api = new API(config.api.cache, config.api.methods);


	var animator = new Animator(100);

	var popCountersViewController;
	var popOnDateViewController;
	var popGrowthViewController;
	var popPyramidViewController;
	var mostPopulousViewController;
	var highestDensityViewController;

	var popoverOverlay;
	var $popoverContainer;
	var $popoverFrame;

	$(function ( )
	{
		// Init API
		api = new API(config.api.cache, config.api.methods);

		$('.share').each(function(index, element) {
			// set text for the links inside each share link container
			var $this = $(this);
			$this.find('a:eq(0)').text(config.learn_more_link_label);
			$this.find('a:eq(1)').text(config.share_link_label);
			if ( $this.find('a:eq(2)') ) {
				$this.find('a:eq(2)').text(config.view_data_table_link_label);
			}
		});

		$('.component-header').find('.share a:eq(0)').click(function(event) {
			// animate to anchor point
			event.preventDefault();
			var anchorTag = $(this).prop('href').split('#')[1];
			var target = $('#' + anchorTag);
			$('body, html').animate({ scrollTop : target.offset().top }, 300);
			return false;
		});

		$('#content-wrapper h2').append(config.component_dividing_header);
	});

	</script>
<?php else: ?>		
	<!-- external libraries -->
	<link rel="stylesheet" href="./js/jquery-ui.custom/css/custom-theme/jquery-ui.custom.min.css" media="screen">

<script src="./js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="./js/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="./js/jquery-ui-themes-1.10.0/themes/base/jquery-ui.css" type="text/javascript"></script>


	
	<?php foreach($externalJSScripts[$component] as $url): ?>		
	<script src="<?php echo $url; ?>" type="text/javascript"></script>	
	<?php endforeach; ?>

	<?php foreach($cssFilesForComponent[$component] as $url): ?>
	<link rel="stylesheet" href="<?php echo $url; ?>" media="screen">
	<?php endforeach; ?>
	

	<script type="text/javascript">
	var config = <?php echo json_encode($config); ?>;
	var api = new API(config.api.cache, config.api.methods);
	<?php if ( $component == 'counter' || $component == 'pyramid' ): ?>
	var animator = new Animator(100);
	<?php endif; ?>

	var popCountersViewController;

	$(function()
	{

		$('.share').each(function(index, element) {
			var $this = $(this);
			$this.find('a:eq(0)').text(config.learn_more_link_label);
			$this.find('a:eq(1)').text(config.share_link_label);
		});

	<?php if ( _PAGE_ == 'embed' ) : ?>
		resize_parent_popover();
	<?php endif; ?>

	});
	</script>
<?php
endif;
?>
