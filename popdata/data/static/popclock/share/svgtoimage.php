<?php
/**
 * Convert SVG to Image
 *
 * @param string $_POST['svg'] SVG from charts
 * @param float $_POST['scale'] Factor to scale the final image
 * @param int $_POST['width'] Width of final image (must be used with height)
 * @param int $_POST['height'] Height of final image (must be used with width)
 * @return string Full URL to image file
 * @author Doug Axelrod <axelrod@homefrontdc.com>
 */
require_once '../includes/common.php';
require_once '../includes/libraries/funct_JSONLibrary.php';

/*
 * Initializing
 */
if ( ! extension_loaded('imagick') ) {
	server_error('Image Magick PHP binding not installed!');
}

if ( extension_loaded('gmagick') ) {
	server_error('gmagick is installed and it shouldn\'t be');
}

/*
 * User Defined Config
 */
// Possible output formats
// 'png24' => '.png',
// 'jpeg' => '.jpg',

// Output format (see options above)
define('OUTPUT_FORMAT', 'png24');

// Absolute path to output folder
define('OUTPUT_FOLDER', _DIR_ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR);

// Absolute path to output url
define('OUTPUT_URL', _URL_ . '/share/images/');

/*
 * Script
 */

/*
 * Validation
 */
// SVG
if ( ! isset($_POST['svg']) // Not set
		|| strlen($_POST['svg']) == 0 // Empty
		|| preg_match('/^<svg.*<\/svg>$/is', $_POST['svg']) == false // Error or doesn't start or end with svg
		) {

	input_error('Bad SVG');
}

if ( ! isset($_POST['name']) // Not set
		|| strlen(trim($_POST['name'])) == 0 // Empty
		) {

	input_error('Bad Name');
}

/*
 * Init
 */
// Get SVG
// We add the <?xml> header because ImageMagick doesn't like it otherwise
$svg = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $_POST['svg'];

// Creates the filename for the SVG
$filename = $_POST['name'];

// Filepath includes the full server path
$filepath = OUTPUT_FOLDER . $filename;

// Url includes the full url
$url = OUTPUT_URL . $filename;

// Test for file existance
if ( ! file_exists($filepath) ) {

	// File needs to be created

	// Create new class
	$im = new Imagick();

	if ( ! is_a($im, 'Imagick') ) {
		server_error('Could not instatiate Imagick class');
	}

	// Read in the SVG
	try {
		$im->readimageblob($svg);
	} catch ( Exception $e ) {
		// I'm going to blame this on the user
		input_error('SVG couldn\'t be read: ' . $e->getMessage());
	}

	// Set Image format
	try {
		$im->setImageFormat(OUTPUT_FORMAT);
	} catch ( Exception $e ) {
		server_error('Could not use output format: ' . OUTPUT_FORMAT . ': ' . $e->getMessage());
	}

	// Save Image
	try {
		$im->writeimage($filepath);
	} catch ( Exception $e ) {
		server_error('Could not write image to: ' . $filepath . ': ' . $e->getMessage());
	}

	// Cleanup
	$im->clear();
	$im->destroy();
}

// Return URL
echo $url;
exit(0);