<?php
/*!
 * JSON decoder/encoder
 * 
 * These functions will be deprecated when an upgrade to PHP 5.2+ is done. 
 * After the upgrade, it is safe to remove this script and the JSON.php
 * that is associated.
 */
if (!function_exists('json_decode')) {
	require_once('JSON.php');
	function json_decode($content, $assoc=false) {
		if ($assoc) {
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		} else {
			$json = new Services_JSON;
		}
		return $json->decode($content);
	}

	function json_encode($content){
		$json = new Services_JSON;
		return $json->encode($content);
	}
}