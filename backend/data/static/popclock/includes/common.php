<?php
/**
 * Common functions and variables
 *
 * @file
 */
define('KEY', 'd1577327606f0b84988a90e638a5a420ddcd9033');


define('API_MODE', 'ER');
// define('API_MODE', 'FR');
// define('API_MODE', 'PROD');


define('US_POP_STATE_VINTAGE', '2021');
define('US_POP_COUNTY_VINTAGE', '2019');
define('US_POP_CITY_VINTAGE', '2019');


/* A cache file in 'popclock/data/' must be newer than the refresh
 * date. Otherwise, a new cache file is generated automatically.
 */
define('COUNTY_POP_DENSITY_REFRESH_DATE', '2025-01-01');
define('CITY_POP_DENSITY_REFRESH_DATE', '2025-01-01');
define('STATE_POP_DENSITY_REFRESH_DATE', '2025-01-01');
define('REGION_REFRESH_DATE', '2025-01-01');


/* This data is supplied to us by CNMP in a spreadsheet. It
 * will contain a list of ~100 years of mid-year population
 * estimates. For the purpose of the odmeter, we need this year
 * and next years data.
 */
$tot_world_pop = array();

/* New projections, provided on 12/10/2021 by CNMP */

/* Projections from 8/2/2023 */
/*
$total_world_pop["2022"] = 7906702795;
$total_world_pop["2023"] = 7982019198; 
$total_world_pop["2024"] = 8057236243; 
$total_world_pop["2025"] = 8131898863; 
$total_world_pop["2026"] = 8205741495; 
*/

/* Projections from 10/25/2024 */
// $total_world_pop["2023"] = 7985160744; 
// $total_world_pop["2024"] = 8059221289; 
// $total_world_pop["2025"] = 8124235044; 
// $total_world_pop["2026"] = 8192462260;
// $total_world_pop["2027"] = 8260874550;

/* Projections from 10/25/2024 */
// $total_world_pop["2023"] = 7985160744;
// $total_world_pop["2024"] = 8059221289;
// $total_world_pop["2025"] = 8124235044;
// $total_world_pop["2026"] = 8192462260;
// $total_world_pop["2027"] = 8260874550;

// $total_world_pop["2023"] = 7985160744;
// $total_world_pop["2024"] = 8056083537;
// $total_world_pop["2025"] = 8127318404;
// $total_world_pop["2026"] = 8197952209;

$total_world_pop["2024"] = 8059221289;
$total_world_pop["2025"] = 8124235044;
$total_world_pop["2026"] = 8192462260;
$total_world_pop["2027"] = 8260874550;

$GLOBALS['world_pop'] = $total_world_pop; 

define('DEBUG', false); // False for Production


// Directory of the requested script
$dir = dirname($_SERVER['SCRIPT_FILENAME']);

// Construct a url based on the current script location
// (5/26/2021: Updated to exclude 'http://'. 
//$url = ( ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
$url = 'https://'. $_SERVER['HTTP_HOST'];

// Request URL
$request = $url . $_SERVER['REQUEST_URI'];

$page = pathinfo($_SERVER['SCRIPT_FILENAME']);

$page = str_replace('.php','',$page['basename']);

if (strpos($page, 'popclock/') !== false) {	
	$page = str_replace('popclock/', '', $page);	
}

$app_root_url = substr($request,0,strpos($request,'popclock') + strlen('popclock') + 1);
// remove all mentions of the protocol.

$app_root_path = str_replace($url,'',$app_root_url);

$new_app_root_path = preg_split('/\/\//',$app_root_url);
//$app_root_url = '//' . $new_app_root_path[1];
//Updated to always include 'https:'
$app_root_url = 'https://' . $new_app_root_path[1];

// Set constants
define('_DIR_', $dir); // /var/www/vhosts/domain.com/share
define('_URL_', $url); // http://domain.com
define('_REQUEST_', $request); // http://domain.com/index.php?arg=1
define('_PAGE_', $page); // index, embed or print
define('_APPROOT_', $app_root_url); // main entry point of the app, so can be used in any directory structure
define('_APPROOTPATH_', $app_root_path); // main entry point of the app, so can be used in any directory structure
define('NEW_BASE_URL', $app_root_path . 'data/population'); // main entry point of the app, so can be used in any directory structure
function server_error ( $message ) {
	header(":", true, 500); // 500 Internal Server Error
	if ( DEBUG ) echo $message;
	exit(1);
}

function input_error ( $message ) {
	header(':', true, 400); // 400 Bad Request
	if ( DEBUG ) echo $message;
	exit(1);
}
