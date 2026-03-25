<?php
require_once 'includes/common.php';
require_once 'includes/config.php';

$is_home_legacy = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
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