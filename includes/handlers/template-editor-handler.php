<?php
/**
 * Handles saving and deleting of booking templates.
 *
 * @file includes/handlers/template-editor-handler.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// Register actions.
add_action( 'admin_post_moosebooking_save_template', 'moosebooking_handle_save_template' );
add_action( 'admin_post_moosebooking_delete_template', 'moosebooking_handle_delete_template' );

/**
 * ============================================================
 * 💾 Save or update booking template
 * ============================================================
 */
function moosebooking_handle_save_template() {

	// --- Security checks ---
	if ( ! isset( $_POST['moosebooking_template_nonce'] ) ||
		! wp_verify_nonce( $_POST['moosebooking_template_nonce'], 'moosebooking_save_template' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'moose-booking' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'moose-booking' ) );
	}

	global $wpdb;
	$table = $wpdb->prefix . 'moosebooking_templates';

	$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
	$name        = moosebooking_sanitize_input( $_POST['template_name'] ?? '' );
	$description = moosebooking_sanitize_input( $_POST['template_description'] ?? '', 'text' );
	$active      = ! empty( $_POST['active'] ) ? 1 : 0;

	// ============================================================
	// 🕒 WEEKLY DEFAULTS (always include all days)
	// ============================================================
	$days = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );

	if ( ! empty( $_POST['weekly_defaults_json'] ) ) {
		$weekly_defaults = json_decode( stripslashes( $_POST['weekly_defaults_json'] ), true );
	} else {
		$weekly_defaults = $_POST['weekly_defaults'] ?? array();
	}

	$cleaned_weekly_defaults = array();

	foreach ( $days as $day ) {
		$day_data = $weekly_defaults[ $day ] ?? array();
		$bookable = ! empty( $day_data['bookable'] );
		$slots    = array();

		if ( ! empty( $day_data['slots'] ) && is_array( $day_data['slots'] ) ) {
			foreach ( $day_data['slots'] as $slot ) {
				$slots[] = array(
					'start' => moosebooking_sanitize_input( $slot['start'] ?? '' ),
					'end'   => moosebooking_sanitize_input( $slot['end'] ?? '' ),
					'price' => floatval( $slot['price'] ?? 0 ),
				);
			}
		}

		// Alla dagar ska finnas i JSON, även om de är tomma
		$cleaned_weekly_defaults[ $day ] = array(
			'bookable' => $bookable,
			'slots'    => $slots,
		);
	}

	// ============================================================
	// 🗓️ CUSTOM DATES
	// ============================================================
	$custom_dates_raw = stripslashes( $_POST['custom_dates'] ?? '' );
	$custom_dates     = json_decode( $custom_dates_raw, true );

	if ( ! is_array( $custom_dates ) ) {
		$custom_dates = array();
	}

	$cleaned_custom_dates = array();
	foreach ( $custom_dates as $date_obj ) {
		if ( empty( $date_obj['date'] ) ) {
			continue;
		}

		$cleaned_custom_dates[] = array(
			'date'      => moosebooking_sanitize_input( $date_obj['date'] ),
			'bookable'  => ! empty( $date_obj['bookable'] ),
			'available' => is_array( $date_obj['available'] ) ? $date_obj['available'] : array(),
			'note'      => isset( $date_obj['note'] ) ? moosebooking_sanitize_input( $date_obj['note'], 'textarea' ) : '',
		);
	}

	// ============================================================
	// 📆 BOOKING LIMITS
	// ============================================================
	$override_limits = isset( $_POST['override_limits'] ) ? 1 : 0;

	// Behåll tidigare lokala värden om override inte är aktivt
	if ( $override_limits ) {
		$max_days_ahead   = isset( $_POST['max_days_ahead'] ) ? intval( $_POST['max_days_ahead'] ) : 30;
		$min_hours_before = isset( $_POST['min_hours_before'] ) ? intval( $_POST['min_hours_before'] ) : 0;

		$max_days_ahead   = max( 1, min( $max_days_ahead, 730 ) );
		$min_hours_before = max( 0, min( $min_hours_before, 168 ) );
	} else {
		// Hämta tidigare sparade värden så de bevaras i databasen
		if ( $template_id ) {
			$existing = $wpdb->get_row( $wpdb->prepare( "SELECT max_days_ahead, min_hours_before FROM $table WHERE id = %d", $template_id ) );
			$max_days_ahead   = intval( $existing->max_days_ahead ?? 30 );
			$min_hours_before = intval( $existing->min_hours_before ?? 0 );
		} else {
			// Ny mall – fallback till globala värden
			$max_days_ahead   = get_option( 'moosebooking_limits_max_bookings_days_ahead', 30 );
			$min_hours_before = get_option( 'moosebooking_limits_min_hours_before_booking', 0 );
		}
	}

	// ============================================================
	// 🚫 UNBOOKABLE PERIODS
	// ============================================================
	$unbookable_dates_input = $_POST['unbookable_dates_text'] ?? '';
	$unbookable_weeks_input = $_POST['unbookable_weeks_text'] ?? '';

	$unbookable_dates = array_filter( array_map( 'trim', explode( ',', $unbookable_dates_input ) ) );
	$unbookable_weeks = array_filter( array_map( 'trim', explode( ',', $unbookable_weeks_input ) ) );

	// ============================================================
	// 🧱 Prepare data for insert/update
	// ============================================================
	$data = array(
		'name'              => $name,
		'description'       => $description,
		'active'            => $active,
		'updated_at'        => current_time( 'mysql' ),
		'custom_dates'      => wp_json_encode( $cleaned_custom_dates ),
		'weekly_defaults'   => wp_json_encode( $cleaned_weekly_defaults ),
		'unbookable_dates'  => wp_json_encode( $unbookable_dates ),
		'unbookable_weeks'  => wp_json_encode( $unbookable_weeks ),
		'max_days_ahead'     => $max_days_ahead,
		'min_hours_before'  => $min_hours_before,
		'override_limits'   => $override_limits,
	);

	// ============================================================
	// 🧩 Insert or Update
	// ============================================================
	if ( $template_id ) {
		$wpdb->update(
			$table,
			$data,
			array( 'id' => $template_id )
		);
	} else {
		$data['created_at'] = current_time( 'mysql' );
		$wpdb->insert( $table, $data );
	}

	// Redirect back to template list
	wp_safe_redirect( admin_url( 'admin.php?page=moosebooking-templates' ) );
	exit;
}

/**
 * ============================================================
 * 🗑️ Delete booking template
 * ============================================================
 */
function moosebooking_handle_delete_template() {

	if ( ! isset( $_GET['template_id'] ) || ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized', 'moose-booking' ) );
	}

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'moosebooking_delete_template' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'moose-booking' ) );
	}

	global $wpdb;
	$table = $wpdb->prefix . 'moosebooking_templates';

	$wpdb->delete(
		$table,
		array( 'id' => intval( $_GET['template_id'] ) )
	);

	wp_safe_redirect( admin_url( 'admin.php?page=moosebooking-templates' ) );
	exit;
}
