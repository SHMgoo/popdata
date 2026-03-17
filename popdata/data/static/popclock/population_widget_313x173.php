<?php

require_once 'includes/common.php';
require_once 'includes/config.php';

$is_widget_313 = true;
$is_widget_200 = false;
$is_widget_264 = false;
?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <title>Population Clock</title>
  <meta charset="UTF-8">
<!--
  <link rel="stylesheet" href="./css/bundle.css" media="screen"> 
 <link rel="stylesheet" href="./css/icons.css" media="screen">
 <link rel="stylesheet" href="./css/fonts.css" media="screen">
-->
 <link rel="stylesheet" href="./css/bundle.css" media="screen"> 
 <link rel="stylesheet" href="./css/iconfont.css" media="screen">
 <link rel="stylesheet" href="./css/lora.css" media="screen">
 <link rel="stylesheet" href="./css/roboto.css" media="screen">
 <link rel="stylesheet" href="./css/roboto-condensed.css" media="screen">


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
