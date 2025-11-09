<?php
/**
 * REST API endpoints for Moose Booking.
 *
 * @file includes/classes/class-moosebooking-rest.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

class MooseBooking_REST {
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	public static function register_routes() {
		register_rest_route(
			'moosebooking/v1',
			'/templates',
			array(
				'methods'  => 'GET',
				'callback' => array( __CLASS__, 'get_templates' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public static function get_templates() {
		global $wpdb;
		$table = $wpdb->prefix . 'moosebooking_templates';
		$results = $wpdb->get_results( "SELECT id, name FROM {$table} WHERE active = 1" );
		return rest_ensure_response( $results );
	}
}
MooseBooking_REST::init();
