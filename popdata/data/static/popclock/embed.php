<?php
	require_once 'includes/common.php';
	require_once 'includes/config.php';

	if ( ! isset($_GET['component']) ) :
		input_error('Component does not exist!');
	else:
		$component = htmlspecialchars($_GET['component']);

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
			'./js/main.min.js',
			'./js/api.min.js',
			'./js/animator.min.js',
			'./js/counter.min.js',
			'./js/odometer.min.js',
			'./js/html_progress_bar.min.js',
			'./js/population.min.js',
			'./population_counters/population_counters_controller.min.js',
			'./js/share.min.js'
		),
		'pop_on_date' => array(
			'./js/jquery-ui.custom/js/jquery-ui.custom.min.js',
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.min.js',
			'./js/api.min.js',
			'./population_on_date/population_on_date_controller.min.js',
			'./js/share.min.js'
		),
		'growth' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/raphael/raphael-min.js',
			'./js/raphael/raphael.export.min.js',
			'./js/raphael/g.raphael.min.js',
			'./js/raphael/g.line.min.js',
			'./js/main.min.js',
			'./js/api.min.js',
			'./js/html_progress_bar.min.js',
			'./js/linechart_with_overlay.min.js',
			'./population_growth/population_growth_controller.min.js',
			'./js/share.min.js'
		),
		'pyramid' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/raphael/raphael-min.js',
			'./js/raphael/raphael.export.min.js',
			'./js/raphael/g.raphael.min.js',
			'./js/raphael/g.line.min.js',
			'./js/main.min.js',
			'./js/api.min.js',
			'./js/animator.min.js',
			'./js/counter.min.js',
			'./js/slider.min.js',
			'./js/pyramid_graph.min.js',
			'./population_pyramid/population_pyramid_controller.min.js',
			'./js/share.min.js'
		),
		'density' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.min.js',
			'./js/api.min.js',
			'./js/tabbed_table.min.js',
			'./highest_density/highest_density_controller.min.js',
			'./js/share.min.js'
		),
		'populous' => array(
			'./js/zeroclipboard/ZeroClipboard.min.js',
			'./js/main.min.js',
			'./js/api.min.js',
			'./js/tabbed_table.min.js',
			'./most_populous/most_populous_controller.min.js',
			'./js/share.min.js'
		)
	);

	$cssFilesForComponent = array(
		'counter' => array(
			'./css/main.min.css',
			'./css/main-embed.min.css',
			'./css/share_this-embed.min.css',
			'./css/population_counter.min.css',
			'./css/population_counter-embed.min.css'
		),
		'pop_on_date' => array(
			'./js/jquery-ui.custom/css/custom-theme/jquery-ui.custom.min.css',
			'./css/main.min.css',
			'./css/main-embed.min.css',
			'./css/share_this-embed.min.css',
			'./css/population_on_date-embed.min.css'
		),
		'growth' => array(
			'./css/main.min.css',
			'./css/main-embed.min.css',
			'./css/share_this-embed.min.css',
			'./css/population_growth-embed.min.css'
		),
		'pyramid' => array(
			'./css/main.min.css',
			'./css/main-embed.min.css',
			'./css/share_this-embed.min.css',
			'./css/population_pyramid-embed.min.css'
		),
		'density' => array(
			'./css/main.min.css',
			'./css/main-embed.min.css',
			'./css/share_this-embed.min.css',
			'./css/populous_density-embed.min.css'
		),
		'populous' => array(
			'./css/main.min.css',
			'./css/main-embed.min.css',
			'./css/share_this-embed.min.css',
			'./css/populous_density-embed.min.css'
		)
	);


	/**
	 * Sharing
	 */
	require_once 'includes/libraries/share.php';

	$share = Share::get_instance();

	// Share text changes between components
	$share_pt_text = $config->share->components->{$component}->pinterest;
	$share_fb_text = $config->share->components->{$component}->facebook;
	$share_tw_text = $config->share->components->{$component}->twitter;
	$share_em_text = $config->share->components->{$component}->email;

	$share_url = $config->share->url;
	if ( isset($_GET['image']) ) {
		$share_image = $_GET['image'];
	} else {
		$share_image = $config->share->image;
	}
	$share_fb_href = _REQUEST_ . '&redirect=1';
	$share_fb_title = $config->share->facebook->title;
	$share_fb_sitename = $config->share->facebook->site_name;
	$share_fb_type = $config->share->facebook->type;
	$share_fb_tags = $config->share->facebook->tags;

	// Set Facebook early for open graph tags
	$share->add('Facebook', 'Facebook')->share($share_fb_text, $share_url, $share_image);
?>
<!DOCTYPE html> 
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<?php
		$facebook = $share->get('Facebook');
		echo $facebook->get_og_tags($share_fb_title, $share_fb_sitename, $share_fb_type, $share_fb_tags);
		if ( isset($_GET['redirect']) ) {
			echo $facebook->get_og_redirect();
		}
	?>
	<script>
		var component     = '<?php echo htmlentities($component, ENT_QUOTES); ?>';
		var share_pt_text = '<?php echo htmlentities($share_pt_text, ENT_QUOTES); ?>';
		var share_fb_text = '<?php echo htmlentities($share_fb_text, ENT_QUOTES); ?>';
		var share_fb_href = '<?php echo htmlentities($share_fb_href, ENT_QUOTES); ?>';
		var share_tw_text = '<?php echo htmlentities($share_tw_text, ENT_QUOTES); ?>';
		var share_em_text = '<?php echo htmlentities($share_em_text, ENT_QUOTES); ?>';
		var share_url     = '<?php echo htmlentities($share_url, ENT_QUOTES); ?>';
		var share_image   = '<?php echo htmlentities($share_image, ENT_QUOTES); ?>';
	</script>
	<?php require_once 'includes/header.php'; ?>
</head>
<body>
<?php require_once 'includes/sitecatalyst.php'; ?>
<?php require_once 'includes/section_508.php'; ?>

<div id="main-wrapper" class="beveled">
	<h1 id="masthead">
		<a href="./" target="_blank">U.S. Census Bureau</a>
		<?php echo $title; ?>
	</h1>
	<div id="content-wrapper">

	<?php
		// individual sharable component
		require_once $template_url;
	?>

	<div id="share-this"></div>

	<?php require_once 'includes/footnotes.php'; ?>

	</div>
	<div class="ffix">&nbsp;</div>
</div>
</body>
</html>
