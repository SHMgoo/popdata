<?php
/**
 * Convert JSON to CSV
 *
 * @param string $_POST['json'] JSON from API
 * @param string $_POST['component']
 *
 * Streams result to browser
 * @author Doug Axelrod <axelrod@homefrontdc.com>
 */
$json_in =  $_POST['json'];
if($json_in != strip_tags($json_in)){
	header('Location: /popclock/index.php');
}

require_once '../includes/common.php';
require_once '../includes/config.php';
require_once '../includes/libraries/funct_JSONLibrary.php';
require_once '../includes/libraries/funct_CSVLibrary.php';

/*
 * Initializing
 */
$components = array(
		'counter',
		'growth',
		'pyramid',
		'populous',
		'density',
);

/*
 * User Defined Config
 */
// Content type of output file
define('OUTPUT_CONTENT_TYPE', 'text/csv');

// Extension of output file
define('OUTPUT_EXTENSION', '.csv');

/*
 * Script
*/


/*
 * Validation
 */
//@todo - json_decode doesn't exist in < PHP 5.2
if ( ! isset($_POST['json']) // Not set
		|| json_decode($_POST['json']) === null // Bad JSON
		) {

	input_error('Bad JSON');
}

if ( ! isset($_POST['component']) // Not set
		|| strlen(trim($_POST['component'])) == 0 // Empty
		|| ! in_array($_POST['component'], $components) // Not in list of known components
		) {

	input_error('Bad Component');
}

/*
 * Init
 */
// JSON
$json = json_decode($_POST['json']);
if ( ! is_array($json) ) {
	input_error('JSON is not an array');
}

// Component
$component = trim($_POST['component']);

// Title
if ( ! isset($config->components->{$component}->label) ) {
	server_error('Config didn\'t contain the proper component label');
}
$filename = $config->components->{$component}->label . OUTPUT_EXTENSION;

$lines = array();
foreach ( $json as $index => $table ) {

	if ( ! is_a($table, 'stdClass') || ! isset($table->title) || ! isset($table->data) ) {
		input_error('Table does not contain title and data attributes');
	}
	if ( count($json) > 1 ) {
		// Add a title if there are more than one tables
		if ( $index > 0 ) {
			// Add a blank line to separate tables (for additional tables)
			$lines[] = array('');
		}
		$lines[] = array($table->title);
	}

	if ( ! is_array($table->data) ) {
		input_error('Data is not an array');
	}
	foreach ( $table->data as $row ) {
		// Add rows directly to the lines
		$lines[] = $row;
	}
}

// Start CSV with empty string
$csv = '';
foreach ( $lines as $line ) {
	$csv .= str_putcsv($line);
}

// Stream file
header("Cache-Control: must-revalidate");
header("Pragma: must-revalidate");
header('Content-Type: ' . OUTPUT_CONTENT_TYPE);
header('Content-Disposition: attachment;filename="' . addslashes($filename) . '"');
echo $csv;
exit(0);
