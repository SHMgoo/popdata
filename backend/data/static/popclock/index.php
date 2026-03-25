<?php
	require_once 'includes/common.php';
	require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Population Clock</title>
        <meta charset="UTF-8">
        <meta name="description" content="Shows estimates of current USA Population overall and people by US state/county and of World Population overall, by country and most populated countries."/>

	<?php require_once 'includes/header.php'; ?>
</head>
<body>
<?php require_once 'includes/section_508.php'; ?>

<!--#include virtual="/main/.in/cb_header.inc"-->

<div id="content-wrapper">

<?php require_once 'includes/tabs.php'; ?>
	
	<?php
	// individual sharable component
	require_once 'population_counters/population_counters.php';
	?>
	
	<?php
	// individual sharable component

	require_once 'population_on_date/population_on_date.php';

	?>
	<!-- Population distributions -->
	<h2></h2>
	<div id="pop-dist-wrapper" class="container vertical-gradient shadow beveled default-border">
		<div class="vertical-gradient-light">
			<div id="population-growth-container" class="lcol">			
			<?php
			// individual sharable component

			require_once 'population_growth/population_growth.php';

			?>			
			</div>
			<div class="rcol">
			<?php
			// individual sharable component
			require_once 'population_pyramid/population_pyramid.php';
			?>
			</div>
			<div class="ffix">&nbsp;</div>
		</div>
		<div class="vertical-gradient-light">
			<div class="lcol">
			<?php
			// individual sharable component
			require_once 'most_populous/most_populous.php';
			?>
			</div>
			<div class="rcol">
			<?php
			// individual sharable component
			require_once 'highest_density/highest_density.php';
			?>
			</div>
			<div class="ffix">&nbsp;</div>
		</div>
	</div>

	<?php require_once 'includes/footnotes.php'; ?>
        <div class="clear">&nbsp;</div><br />
  </div>
  <br />
  <br />

<!--#include virtual="/main/.in/cb_footer.inc"-->
</div>

</div>
<?php require_once 'includes/popover.php'; ?>

</body>
</html>
