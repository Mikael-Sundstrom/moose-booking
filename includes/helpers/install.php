<?php
/**
 * Handles installation and initial setup of Moose Booking.
 *
 * @file includes/helpers/install.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Run on plugin activation – creates or updates database tables.
 */
function moosebooking_install() {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'moosebooking_templates';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(255) NOT NULL,
		active TINYINT(1) DEFAULT 0,
		description TEXT DEFAULT NULL,
		weekly_defaults LONGTEXT DEFAULT NULL,
		custom_dates LONGTEXT DEFAULT NULL,
		unbookable_dates LONGTEXT DEFAULT NULL,
		unbookable_weeks LONGTEXT DEFAULT NULL,
		override_limits TINYINT(1) DEFAULT 0,              -- If template uses its own rules
		min_hours_before INT UNSIGNED DEFAULT 0,           -- Minimum hours before booking (can be global fallback)
		max_days_ahead INT UNSIGNED DEFAULT 30,            -- Maximum days ahead (can be global fallback)
		created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

/**
 * Clean-up placeholder (extend later if you add uninstall logic)
 */
function moosebooking_deactivate() {
	// You might later want to clear transient data or flush rewrite rules here.
	return;
}
