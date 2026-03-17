<?php
/**
 * In case they ever add a function of this name
 */
if( ! function_exists('str_putcsv') ) {

	function str_putcsv( $input, $delimiter = ',', $enclosure = '"' ) {
		// Open a memory "file" for read/write...
		$fp = fopen('php://temp', 'r+');
		// ... write the $input array to the "file" using fputcsv()...
		fputcsv($fp, $input, $delimiter, $enclosure);
		// ... rewind the "file" so we can read what we just wrote...
		rewind($fp);
		// ... read the entire line into a variable...
		$data = fgets($fp);
		// ... close the "file"...
		fclose($fp);
		// ... and return the $data to the caller, leaving the trailing newline from fgets().
		return $data;
	}
}