<?php
$url =  $_SERVER['PHP_SELF'];
if($url != strip_tags($url)){
	header('Location: /popclock/index.php');
}
require_once '../includes/common.php';
require_once ('/vs/www/php-bin/app-config/popclock/bootstrap.inc.php');


require_once (dirname(__FILE__) . '/population.class.php');
//CBDB::connect($db);

$pop = new population ();
header("Access-Control-Allow-Origin: *");
echo $pop;
