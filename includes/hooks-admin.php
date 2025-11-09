<?php
/**
 * Handles hooks and logic specific to the WordPress admin panel.
 *
 * @file includes/hooks-admin.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register plugin settings.
 */
add_action(
	'admin_init',
	function () {
		// Email settings.
		register_setting( 'moosebooking_email_settings_group', 'moosebooking_email_to_admin' );
		register_setting( 'moosebooking_email_settings_group', 'moosebooking_email_message_subject' );
		register_setting( 'moosebooking_email_settings_group', 'moosebooking_email_user_message' );
		register_setting( 'moosebooking_email_settings_group', 'moosebooking_email_notify_admin' );

		// Appearance settings.
		register_setting( 'moosebooking_appearance_settings_group', 'moosebooking_appearance_primary_color' );
		register_setting( 'moosebooking_appearance_settings_group', 'moosebooking_appearance_calendar_view' );
		register_setting( 'moosebooking_appearance_settings_group', 'moosebooking_appearance_show_week_numbers' );

		// Limits settings.
		register_setting( 'moosebooking_limits_settings_group', 'moosebooking_limits_max_bookings_per_ip' );
		register_setting( 'moosebooking_limits_settings_group', 'moosebooking_limits_min_hours_before_booking' );
		register_setting( 'moosebooking_limits_settings_group', 'moosebooking_limits_max_days_ahead' );

		// Notifications (Twilio) settings.
		register_setting( 'moosebooking_twilio_settings_group', 'moosebooking_twilio_account_sid' );
		register_setting( 'moosebooking_twilio_settings_group', 'moosebooking_twilio_auth_token' );
		register_setting( 'moosebooking_twilio_settings_group', 'moosebooking_twilio_to_phone_number' );
		register_setting( 'moosebooking_twilio_settings_group', 'moosebooking_twilio_from_phone_number' );
		register_setting( 'moosebooking_twilio_settings_group', 'moosebooking_twilio_sms_notify_admin' );
	}
);

/**
 * Enqueue admin scripts and styles.
 */
add_action(
	'admin_enqueue_scripts',
	function () {

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		/****************************************************************
		 * Global resources for all Moose Booking admin pages
		 ****************************************************************/
		if ( str_contains( $screen->id, 'moosebooking' ) ) {

			// Global JS.
			wp_enqueue_script(
				'moosebooking-admin-global',
				MOOSEBOOKING_URL . 'assets/javascripts/admin-moosebooking.js',
				array(),
				filemtime( MOOSEBOOKING_PATH . 'assets/javascripts/admin-moosebooking.js' ),
				true
			);

			// Global CSS (lightweight).
			wp_enqueue_style(
				'moosebooking-admin-global',
				MOOSEBOOKING_URL . 'assets/stylesheets/admin-moosebooking.css',
				array(),
				filemtime( MOOSEBOOKING_PATH . 'assets/stylesheets/admin-moosebooking.css' )
			);

			// Global AJAX variables (accessible on all pages).
			wp_localize_script(
				'moosebooking-admin-global',
				'moosebooking_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'moosebooking_nonce' ),
				)
			);
		}

		/****************************************************************
		 * Specific resources
		 ****************************************************************/
		switch ( $screen->id ) {

			// Template list.
			/* case 'booking_page_moosebooking-templates':
				wp_enqueue_script(
					'moosebooking-admin-template-index',
					MOOSEBOOKING_URL . 'assets/javascripts/admin-template-index.js',
					array(),
					filemtime( MOOSEBOOKING_PATH . 'assets/javascripts/admin-template-index.js' ),
					true
				);
				break; */

			// Template editor.
			case 'booking_page_moosebooking-template-editor':
				wp_enqueue_style(
					'moosebooking-admin-template-editor',
					MOOSEBOOKING_URL . 'assets/stylesheets/admin-template-editor.css',
					array(),
					filemtime( MOOSEBOOKING_PATH . 'assets/stylesheets/admin-template-editor.css' )
				);

				wp_enqueue_script(
					'moosebooking-admin-template-editor',
					MOOSEBOOKING_URL . 'assets/javascripts/admin-template-editor.js',
					array(),
					filemtime( MOOSEBOOKING_PATH . 'assets/javascripts/admin-template-editor.js' ),
					true
				);

				wp_localize_script(
					'moosebooking-admin-template-editor',
					'moosebooking_strings',
					array(
						'type'    => __( 'Type', 'moose-booking' ),
						'price'   => __( 'Price', 'moose-booking' ),
						'comment' => __( 'Comment', 'moose-booking' ),
						'remove'  => __( '×', 'moose-booking' ),
						'hourly'  => __( 'Hourly', 'moose-booking' ),
						'daily'   => __( 'Daily', 'moose-booking' ),
						'weekend' => __( 'Weekend', 'moose-booking' ),
						'weekly'  => __( 'Weekly', 'moose-booking' ),
						'custom'  => __( 'Custom', 'moose-booking' ),
					)
				);
				break;
		}
	}
);

/**
 * Highlight correct submenu when editing a template.
 */
add_filter(
	'submenu_file',
	function ( $submenu_file ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just reading page param.
		if ( isset( $_GET['page'] ) && 'moosebooking-template-editor' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
			return 'moosebooking-templates';
		}
		return $submenu_file;
	}
);

/**
 * AJAX: Generate monthly calendar.
 */
add_action(
	'wp_ajax_moosebooking_generate_calendar',
	function () {
		check_ajax_referer( 'moosebooking_nonce', 'nonce' );

		$year  = isset( $_POST['year'] ) ? intval( $_POST['year'] ) : 0;
		$month = isset( $_POST['month'] ) ? intval( $_POST['month'] ) : 0;

		if ( ! $year || ! $month ) {
			wp_send_json_error( __( 'Invalid year or month.', 'moose-booking' ) );
			wp_die();
		}

		$template_id  = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
		$custom_dates = array();

		// If custom dates provided from JS.
		if ( ! empty( $_POST['custom_dates'] ) ) {
			$decoded_data = json_decode( stripslashes( $_POST['custom_dates'] ), true );
			$custom_dates = is_array( $decoded_data ) ? $decoded_data : array();

		// Otherwise, fetch from DB.
		} elseif ( $template_id ) {
			global $wpdb;
			$table = $wpdb->prefix . 'moosebooking_templates';
			$template = $wpdb->get_row(
				$wpdb->prepare( "SELECT custom_dates FROM {$table} WHERE id = %d", $template_id )
			);

			if ( $template && $template->custom_dates ) {
				$decoded_data = json_decode( $template->custom_dates, true );
				$custom_dates = is_array( $decoded_data ) ? $decoded_data : array();
			}
		}

		$html = moosebooking_render_calendar( $year, $month, $custom_dates );
		echo wp_kses_post( $html );
		wp_die();
	}
);
