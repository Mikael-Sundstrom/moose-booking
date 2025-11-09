<?php
/**
 * Handles frontend-specific hooks with conditional logic.
 *
 * @file includes/hooks-frontend.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// Execute only on the frontend (not in the admin panel)
if ( ! is_admin() ) {

	/**
	 * ============================================================
	 * 🎨 Enqueue frontend scripts & styles
	 * ============================================================
	 */
	add_action(
		'wp_enqueue_scripts',
		function () {

			// Enqueue CSS
			wp_enqueue_style(
				'moosebooking-frontend',
				MOOSEBOOKING_URL . 'assets/stylesheets/frontend-moosebooking.css',
				array(),
				filemtime( MOOSEBOOKING_PATH . 'assets/stylesheets/frontend-moosebooking.css' )
			);

			// Enqueue JS
			wp_enqueue_script(
				'moosebooking-frontend',
				MOOSEBOOKING_URL . 'assets/javascripts/frontend-moosebooking.js',
				array( 'jquery' ),
				filemtime( MOOSEBOOKING_PATH . 'assets/javascripts/frontend-moosebooking.js' ),
				true
			);

			// Passa data till JS
			wp_localize_script(
				'moosebooking-frontend',
				'moosebooking_frontend',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'moosebooking_nonce' ),
				)
			);
		}
	);

	/**
	 * ============================================================
	 * ⚡ Example AJAX handler (optional – placeholder)
	 * ============================================================
	 */
	add_action(
		'wp_ajax_nopriv_moosebooking_fetch_template',
		'moosebooking_ajax_fetch_template'
	);
	add_action(
		'wp_ajax_moosebooking_fetch_template',
		'moosebooking_ajax_fetch_template'
	);

	/**
	 * Simple example: fetch booking template via AJAX.
	 */
	function moosebooking_ajax_fetch_template() {
		check_ajax_referer( 'moosebooking_nonce', 'nonce' );

		global $wpdb;
		$template_name = sanitize_text_field( $_POST['template'] ?? '' );
		if ( ! $template_name ) {
			wp_send_json_error( __( 'Template not specified.', 'moose-booking' ) );
		}

		$table    = $wpdb->prefix . 'moosebooking_templates';
		$template = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", $template_name )
		);

		if ( ! $template ) {
			wp_send_json_error( __( 'Template not found.', 'moose-booking' ) );
		}

		wp_send_json_success( $template );
	}

}
