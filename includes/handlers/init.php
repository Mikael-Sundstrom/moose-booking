<?php
/**
 * Loads all form handlers for the plugin.
 *
 * @file includes/handlers/init.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// List of form handlers.
$handlers = array(
	'template-editor-handler.php',
);

foreach ( $handlers as $handler ) {
	$file_path = MOOSEBOOKING_PATH . 'includes/handlers/' . $handler;

	// Safely include if file exists.
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	} else {
		// Optional: log missing files during development.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( sprintf( '[Moose Booking] Missing handler file: %s', $file_path ) );
		}
	}
}
