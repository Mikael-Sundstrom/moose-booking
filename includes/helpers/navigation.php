<?php
/**
 * Functions for handling navigation and tabs.
 *
 * @file includes/helpers/navigation.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the currently active admin tab.
 *
 * @return string The current tab, defaults to 'menu'.
 */
function moosebooking_get_current_tab() {
	return isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'menu'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
