<?php
/**
 * Settings tab for booking limits.
 *
 * @file pages/settings/tabs/limits.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// Handle form submission manually (to avoid reliance on settings API registration elsewhere)
if ( isset( $_POST['moosebooking_save_limits'] ) && check_admin_referer( 'moosebooking_save_limits_nonce' ) ) {

	update_option(
		'moosebooking_limits_max_bookings_per_ip',
		intval( $_POST['moosebooking_limits_max_bookings_per_ip'] ?? 0 )
	);

	update_option(
		'moosebooking_limits_min_hours_before_booking',
		intval( $_POST['moosebooking_limits_min_hours_before_booking'] ?? 0 )
	);

	update_option(
		'moosebooking_limits_max_days_ahead',
		intval( $_POST['moosebooking_limits_max_days_ahead'] ?? 0 )
	);

	echo '<div class="updated notice is-dismissible"><p>' .
		esc_html__( 'Limit settings saved.', 'moose-booking' ) .
		'</p></div>';
}

// Retrieve saved options
$max_bookings_per_ip       = get_option( 'moosebooking_limits_max_bookings_per_ip', 0 );
$min_hours_before_booking  = get_option( 'moosebooking_limits_min_hours_before_booking', 0 );
$max_days_ahead            = get_option( 'moosebooking_limits_max_days_ahead', 0 );
?>

<h2><?php esc_html_e( 'Begränsningar', 'moose-booking' ); ?></h2>

<form method="post">
	<?php wp_nonce_field( 'moosebooking_save_limits_nonce' ); ?>

	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<label for="moosebooking_limits_max_bookings_per_ip">
					<?php esc_html_e( 'Amount of bookings per IP per day.', 'moose-booking' ); ?>
				</label>
			</th>
			<td>
				<input type="number" name="moosebooking_limits_max_bookings_per_ip"
					id="moosebooking_limits_max_bookings_per_ip"
					min="0" value="<?php echo esc_attr( $max_bookings_per_ip ); ?>"
					class="small-text" />
				<p class="description">
					<?php esc_html_e( 'Set 0 to allow unlimited bookings per IP address.', 'moose-booking' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="moosebooking_limits_min_hours_before_booking">
					<?php esc_html_e( 'Minimum hours before booking can be made.', 'moose-booking' ); ?>
				</label>
			</th>
			<td>
				<input type="number" name="moosebooking_limits_min_hours_before_booking"
					id="moosebooking_limits_min_hours_before_booking"
					min="0" value="<?php echo esc_attr( $min_hours_before_booking ); ?>"
					class="small-text" />
				<p class="description">
					<?php esc_html_e( 'Ensures that bookings are not made too close to the start time.', 'moose-booking' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="moosebooking_limits_max_days_ahead">
					<?php esc_html_e( 'Max amount of days in the future that can be booked.', 'moose-booking' ); ?>
				</label>
			</th>
			<td>
				<input type="number" name="moosebooking_limits_max_days_ahead"
					id="moosebooking_limits_max_days_ahead"
					min="0" value="<?php echo esc_attr( $max_days_ahead ); ?>"
					class="small-text" />
				<p class="description">
					<?php esc_html_e( 'Enter 0 to allow bookings as far in advance as possible.', 'moose-booking' ); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button( __( 'Save settings', 'moose-booking' ), 'primary', 'moosebooking_save_limits' ); ?>
</form>
