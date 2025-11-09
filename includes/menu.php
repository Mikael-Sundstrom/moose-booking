<?php
/**
 * Handles hooks and logic specific to the WordPress admin panel.
 *
 * @file includes/hooks-admin.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		// Main menu.
		add_menu_page(
			esc_html__( 'Booking', 'moose-booking' ),
			esc_html__( 'Booking', 'moose-booking' ),
			'manage_options',
			'moosebooking',
			'moosebooking_render_overview_page',
			'dashicons-calendar-alt',
			25
		);

		// Submenus.

		// Overview (front page).
		add_submenu_page(
			'moosebooking',
			esc_html__( 'Overview', 'moose-booking' ),
			esc_html__( 'Overview', 'moose-booking' ),
			'manage_options',
			'moosebooking',
			'moosebooking_render_overview_page'
		);

		// Bookings.
		add_submenu_page(
			'moosebooking',
			esc_html__( 'Bookings', 'moose-booking' ),
			esc_html__( 'Bookings', 'moose-booking' ),
			'manage_options',
			'moosebooking-bookings',
			'moosebooking_render_bookings_page'
		);

		// Templates.
		add_submenu_page(
			'moosebooking',
			esc_html__( 'Templates', 'moose-booking' ),
			esc_html__( 'Templates', 'moose-booking' ),
			'manage_options',
			'moosebooking-templates',
			'moosebooking_render_forms_page'
		);
		add_submenu_page(
			'moosebooking',
			__( 'Edit Template', 'moose-booking' ),
			__( '- Edit Template', 'moose-booking' ),
			'manage_options',
			'moosebooking-template-editor',
			'moosebooking_render_template_editor_page'
		);

		// Settings.
		add_submenu_page(
			'moosebooking',
			esc_html__( 'Settings', 'moose-booking' ),
			esc_html__( 'Settings', 'moose-booking' ),
			'manage_options',
			'moosebooking-settings',
			'moosebooking_render_settings_page'
		);
	}
);


/**
 * Renders the overview page.
 *
 * @return void
 */
function moosebooking_render_overview_page() {
	require_once plugin_dir_path( __FILE__ ) . '../pages/overview/index.php';
}

/**
 * Templates.
 */
function moosebooking_render_forms_page() {
	require_once plugin_dir_path( __FILE__ ) . '../pages/templates/index.php';
}

/**
 * Template editor.
 */
function moosebooking_render_template_editor_page() {
	require_once plugin_dir_path( __FILE__ ) . '/../pages/templates/template-editor.php';
}

/**
 * Bookings.
 */
function moosebooking_render_bookings_page() {
	require_once plugin_dir_path( __FILE__ ) . '../pages/bookings/index.php';
}

/**
 * Settings.
 */
function moosebooking_render_settings_page() {
	require_once plugin_dir_path( __FILE__ ) . '../pages/settings/index.php';
}
