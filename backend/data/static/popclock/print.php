<?php
	require_once 'includes/common.php';
	require_once 'includes/config.php';


        /* The query string could be empty if this script is called 
         * stand alone. If that happens a default setting is assigned.
         */
        if ( ! isset($_GET['component']) )  {
            $component = 'counter'; 
        } else {
            $component = $_GET['component'];
        }

	if ($component == 'counter'):
		$mainProgresBarWidth = 150;
		$componentProgressBarWidth = 75;
		$progressBarHeight = 10;
		$template_url = 'population_counters/population_counters.php';
		$title = 'Current Population';

	elseif ($component == 'pop_on_date'):
		$template_url = 'population_on_date/population_on_date.php';
		$title = 'Population on a Date';

	elseif ($component == 'growth'):
		$template_url = 'population_growth/population_growth.php';
		$title = 'United States Population Growth by Region';

	elseif ($component == 'pyramid'):
		$template_url = 'population_pyramid/population_pyramid.php';
		$title = 'United States Population by Age and Sex';

	elseif ($component == 'populous'):
		$template_url = 'most_populous/most_populous.php';
		$title = 'Most Populous';

	elseif ($component == 'density'):
		$template_url = 'highest_density/highest_density.php';
		$title = 'Highest Density';

	else:
		input_error('Bad component');
	endif;

	if (isset($_GET['date'])):
		preg_match('/(.{4})(.{2})(.{2})/', $_GET['date'], $date);
		$year = intval($date[1]);
		$month = intval($date[2]) - 1;
		$day = intval($date[3]);
	endif;

	$externalJSScripts = array(
		'counter' => array(
			'./js/jquery-ui.custom/js/jquery-ui.custom.min.js',
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.js',
			'./js/api.js',
			'./js/animator.js',
			'./js/counter.js',
			'./js/odometer.js',
			'./js/html_progress_bar.js',
			'./js/population.js',
			'./population_counters/population_counters_controller.min.js',
			'./js/share.js'
		),
		'pop_on_date' => array(
			'./js/jquery-ui.custom/js/jquery-ui.custom.min.js',
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.js',
			'./js/api.js',
			'./population_on_date/population_on_date_controller.min.js',
			'./js/share.js'
		),
		'growth' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/raphael/raphael-min.js',
			'./js/raphael/raphael.export.js',
			'./js/raphael/g.raphael.js',
			'./js/raphael/g.line.js',
			'./js/main.js',
			'./js/api.js',
			'./js/html_progress_bar.js',
			'./js/linechart_with_overlay.js',
			'./population_growth/population_growth_controller.min.js',
			'./js/share.js'
		),
		'pyramid' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/raphael/raphael-min.js',
			'./js/raphael/raphael.export.js',
			'./js/raphael/g.raphael.js',
			'./js/raphael/g.line.js',
			'./js/main.js',
			'./js/api.js',
			'./js/animator.js',
			'./js/counter.js',
			'./js/slider.js',
			'./js/pyramid_graph.js',
			'./population_pyramid/population_pyramid_controller.min.js',
			'./js/share.js'
		),
		'density' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.js',
			'./js/api.js',
			'./js/tabbed_table.js',
			'./highest_density/highest_density_controller.min.js',
			'./js/share.js'
		),
		'populous' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.js',
			'./js/api.js',
			'./js/tabbed_table.js',
			'./most_populous/most_populous_controller.min.js',
			'./js/share.js'
		)
	);

	$cssFilesForComponent = array(
		'counter' => array(
			'./css/main-print.css',
			'./css/population_counter-print.css'
		),
		'pop_on_date' => array(
			'./css/main-print.css',
			'./css/share_this-print.css',
			'./css/population_on_date-print.css'
		),
		'growth' => array(
			'./css/main-print.css',
			'./css/population_growth-print.css'
		),
		'pyramid' => array(
			'./css/main-print.css',
			'./css/population_pyramid-print.css'
		),
		'density' => array(
			'./css/main-print.css',
			'./css/populous_density-print.css'
		),
		'populous' => array(
			'./css/main-print.css',
			'./css/populous_density-print.css'
		)
	);

?>
<!DOCTYPE html> 
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<?php require_once 'includes/header.php'; ?>
</head>
<body>
<?php require_once 'includes/sitecatalyst.php'; ?>
<?php require_once 'includes/section_508.php'; ?>

<div id="main-wrapper" class="beveled">
	<h1 id="masthead">
		<img src="<?php echo _APPROOT_;?>images/census-logo-whiteBG.png" width="120" height="90"/>
		<a href="./">U.S. Census Bureau</a>
		<?php echo $title; ?>
	</h1>
	<div id="content-wrapper">

	<?php
		// individual sharable component
		require_once $template_url;
	?>

	<?php require_once 'includes/footnotes.php'; ?>

	</div>
	<div class="ffix">&nbsp;</div>
</div>
</body>
</html>
