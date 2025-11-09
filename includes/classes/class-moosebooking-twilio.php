<?php
/**
 * Class for Twilio SMS integration.
 *
 * @file includes/classes/class-moosebooking-twilio.php
 * @package moose-booking
 */
defined( 'ABSPATH' ) || exit;

/**
 * Handles Twilio SMS integration for Moose Booking.
 */
class MooseBooking_Twilio {
	const ERROR_NOT_CONFIGURED  = 'Twilio settings are missing.';
	const ERROR_INVALID_NUMBER  = 'Invalid recipient phone number.';
	const ERROR_GENERIC_FAILURE = 'Failed to send SMS.';

	private $sid;
	private $token;
	private $from;
	private $last_response;

	public function __construct() {
		$this->sid   = get_option( 'moosebooking_twilio_account_sid' );
		$this->token = get_option( 'moosebooking_twilio_auth_token' );
		$this->from  = get_option( 'moosebooking_twilio_from_phone_number' );
	}

	/**
	 * Checks if the Twilio configuration is complete.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->sid ) && ! empty( $this->token ) && ! empty( $this->from );
	}

	/**
	 * Validates a phone number (basic E.164 check).
	 *
	 * @param string $number
	 * @return bool
	 */
	public function is_valid_number( $number ) {
		return preg_match( '/^\+\d{8,15}$/', $number );
	}

	/**
	 * Sends an SMS via Twilio.
	 *
	 * @param string $to Recipient phone number in E.164 format
	 * @param string $message Text message (max 320 chars)
	 * @return true|string True on success, or error string
	 */
	public function send_sms( $to, $message ) {
		if ( ! $this->is_configured() ) {
			return __( 'Twilio settings are missing.', 'moose-booking' );
		}

		if ( ! $this->is_valid_number( $to ) ) {
			return __( 'Invalid recipient phone number.', 'moose-booking' );
		}

		// ------------------------------------------------------------
		// 🔒 Frequency limiter (to prevent SMS spam)
		// ------------------------------------------------------------
		$limit = get_option( 'moosebooking_twilio_sms_frequency_limit', 'none' );
		$intervals = array(
			'hourly' => HOUR_IN_SECONDS,
			'daily'  => DAY_IN_SECONDS,
		);

		if ( $limit !== 'none' && isset( $intervals[ $limit ] ) ) {
			$last_sent = get_transient( 'moosebooking_twilio_last_sms_sent' );

			if ( $last_sent && ( time() - $last_sent < $intervals[ $limit ] ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Moose Booking] SMS skipped due to frequency limit (' . $limit . ').' );
				}
				return __( 'SMS limit reached — no new message sent.', 'moose-booking' );
			}

			set_transient( 'moosebooking_twilio_last_sms_sent', time(), $intervals[ $limit ] );
		}

		// ------------------------------------------------------------
		// 📨 Actual Twilio request
		// ------------------------------------------------------------
		$message = mb_substr( $message, 0, 320 );
		$url     = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( "{$this->sid}:{$this->token}" ),
				),
				'body'    => array(
					'To'   => $to,
					'From' => $this->from,
					'Body' => $message,
				),
				'timeout' => 15,
			)
		);

		$this->last_response = $response;

		if ( is_wp_error( $response ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Moose Booking] Twilio SMS error: ' . $response->get_error_message() );
			}
			return $response->get_error_message();
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code >= 200 && $code < 300 ) {
			return true;
		}

		$body      = json_decode( wp_remote_retrieve_body( $response ), true );
		$error_msg = ! empty( $body['message'] ) ? $body['message'] : __( 'Failed to send SMS.', 'moose-booking' );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[Moose Booking] Twilio SMS API error: ' . $error_msg );
		}
		return $error_msg;
	}


	/**
	 * Retrieves the account balance from Twilio (cached via transient for 5 min).
	 *
	 * @return array|false Balance data or false on failure
	 */
	public function get_balance() {
		if ( empty( $this->sid ) || empty( $this->token ) ) {
			return false;
		}

		// Use a transient key that is unique per account SID.
		$cache_key = 'moosebooking_twilio_balance_' . md5( $this->sid );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Balance.json";

		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for HTTP Basic Authentication with Twilio API, not code obfuscation.
					'Authorization' => 'Basic ' . base64_encode( "{$this->sid}:{$this->token}" ),
				),
				'timeout' => 15,
			)
		);

		$this->last_response = $response;

		if ( is_wp_error( $response ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Conditional debug logging only when WP_DEBUG is enabled.
				error_log( '[Moose Booking] Twilio balance error: ' . $response->get_error_message() );
			}
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $data && isset( $data['balance'] ) ) {
			// Cache balance result for 15 minutes.
			set_transient( $cache_key, $data, 15 * MINUTE_IN_SECONDS );
		}

		return $data ? $data : false;
	}

	/**
	 * Refreshes the cached balance by deleting the transient and fetching anew.
	 *
	 * @return array|false Balance data or false on failure
	 */
	public function refresh_balance() {
		delete_transient( 'moosebooking_twilio_balance_' . md5( $this->sid ) );
		return $this->get_balance();
	}

	/**
	 * Returns the last API response (raw) for debugging.
	 *
	 * @return mixed
	 */
	public function get_last_response() {
		return $this->last_response;
	}
}

// $twilio = new MooseBooking_Twilio();
// if ($twilio->is_configured()) {
//     $result = $twilio->send_sms('+46700000000', 'New booking received!');
//     if ($result === true) {
//         echo 'SMS sent!';
//     } else {
//         echo 'Error: ' . esc_html($result);
//     }
// }
// else {
//     echo 'Twilio is not configured.';
// }
