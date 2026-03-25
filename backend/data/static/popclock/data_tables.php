<?php
	require_once 'includes/common.php';
	require_once 'includes/config.php';
	
	if ( ! isset($_GET['component']) ) :
		input_error('Component does not exist!');
	else:
		$component = in_array($_GET['component'],array('growth','pyramid')) ? $_GET['component']  : null ;

		if ($component == 'growth'):
			$template_url = 'population_growth/population_growth.php';
			$title = 'United States Population Growth by Region';

		elseif ($component == 'pyramid'):
			$template_url = 'population_pyramid/population_pyramid.php';
			$title = 'United States Population by Age and Sex';

		endif;

	endif;

	$externalJSScripts = array(
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
			'./js/raphael/g.line..minjs',
			'./js/main.min.js',
			'./js/api.min.js',
			'./js/animator.min.js',
			'./js/counter.min.js',
			'./js/slider.min.js',
			'./js/pyramid_graph.min.js',
			'./population_pyramid/population_pyramid_controller.min.js',
			'./js/share.min.js'
		),
	);

	$cssFilesForComponent = array(
		'growth' => array(
			'./css/main.min.css',
			'./css/population_growth-data_tables.min.css'
		),
		'pyramid' => array(
			'./css/main.min.css',
			'./css/population_pyramid-data_tables.min.css'
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
<?php require_once 'includes/section_508.php'; ?>

<?php
	// individual sharable component
	require_once $template_url;
?>

<?php require_once 'includes/footnotes.php'; ?>

</body>
</html>
