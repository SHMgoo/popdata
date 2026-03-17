<?php

require_once 'includes/common.php';
require_once 'includes/config.php';

$is_widget_200 = false;
$is_widget_264 = false;
$is_widget_310 = true;
$is_widget_313 = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Population Clock</title>
	<?php require_once 'includes/header_home.php'; ?>
</head>
<body>
<?php require_once 'includes/sitecatalyst.php'; ?>
<?php require_once 'includes/section_508.php'; ?>

<?php
// individual sharable component
require_once 'population_counters/population_counters.php';
?>
</body>
</html>

