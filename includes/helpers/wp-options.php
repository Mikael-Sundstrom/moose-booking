<?php
/**
 * Functions for handling database wp_options.
 *
 * @file includes/helpers/wp-options.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fetch all option names starting with a given prefix.
 *
 * @param string $prefix The prefix to search for.
 * @return array An array of matching option names.
 */
function moosebooking_get_option_names_by_prefix( $prefix ) {
	global $wpdb;

	// Try to get the option names from cache
	$cache_key    = 'moosebooking_options_list_' . md5( $prefix );
	$option_names = wp_cache_get( $cache_key, 'moosebooking_cache' );

	if ( false === $option_names ) {
		// Get all option names starting with the prefix.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$option_names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$prefix . '%'
			)
		);

		// Store in cache for 24 hours.
		wp_cache_set( $cache_key, $option_names, 'moosebooking_cache', 86400 );
	}

	return $option_names;
}

/**
 * Deletes all options that start with a specific prefix and returns deleted option names.
 *
 * @param string $prefix The prefix of options to delete.
 * @return array An array of deleted option names.
 */
function moosebooking_delete_options_by_prefix( $prefix ) {
	$option_names    = moosebooking_get_option_names_by_prefix( $prefix );
	$deleted_options = array();

	if ( ! empty( $option_names ) ) {
		foreach ( $option_names as $option_name ) {
			if ( delete_option( $option_name ) ) {
				$deleted_options[] = $option_name;
			}
		}

		// Clear the cache.
		wp_cache_delete( 'moosebooking_options_list_' . md5( $prefix ), 'moosebooking_cache' );
	}

	return $deleted_options; // Return an array of deleted option names.
}
