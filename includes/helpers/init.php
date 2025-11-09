<?php
/**
 * Loads all helper files for the plugin.
 *
 * @file includes/helpers/init.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// List of helper files to load.
$helper_files = array(
	// 'files.php',
	'install.php',
	'navigation.php',
	'redirect.php',
	'validation.php',
	'wp-options.php',
);

// Load each helper file.
foreach ( $helper_files as $file ) {
	$file_path = MOOSEBOOKING_PATH . 'includes/helpers/' . $file;
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}
