<?php
/**
 * General settings tab.
 *
 * @file pages/settings/tabs/general.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// Save form submission.
if ( isset( $_POST['moosebooking_save_general'] ) && check_admin_referer( 'moosebooking_save_general_nonce' ) ) {
	update_option( 'moosebooking_week_start', sanitize_text_field( $_POST['moosebooking_week_start'] ?? 'monday' ) );
	update_option( 'moosebooking_week1_rule', sanitize_text_field( $_POST['moosebooking_week1_rule'] ?? 'iso_standard' ) );
	update_option(
		'moosebooking_default_timezone',
		sanitize_text_field( $_POST['moosebooking_default_timezone'] ?? get_option( 'timezone_string', 'Europe/Stockholm' ) )
	);

	echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'moose-booking' ) . '</p></div>';
}
?>

<h2><?php esc_html_e( 'General', 'moose-booking' ); ?></h2>

<form method="post">
	<?php wp_nonce_field( 'moosebooking_save_general_nonce' ); ?>

	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'Week starts on', 'moose-booking' ); ?></th>
			<td>
				<select name="moosebooking_week_start">
					<option value="monday" <?php selected( get_option( 'moosebooking_week_start' ), 'monday' ); ?>><?php esc_html_e( 'Monday', 'moose-booking' ); ?></option>
					<option value="sunday" <?php selected( get_option( 'moosebooking_week_start' ), 'sunday' ); ?>><?php esc_html_e( 'Sunday', 'moose-booking' ); ?></option>
				</select>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php esc_html_e( 'Week 1 rule', 'moose-booking' ); ?></th>
			<td>
				<select name="moosebooking_week1_rule">
					<option value="iso_standard" <?php selected( get_option( 'moosebooking_week1_rule' ), 'iso_standard' ); ?>><?php esc_html_e( 'ISO Standard (first Thursday)', 'moose-booking' ); ?></option>
					<option value="first_full_week" <?php selected( get_option( 'moosebooking_week1_rule' ), 'first_full_week' ); ?>><?php esc_html_e( 'First full week', 'moose-booking' ); ?></option>
				</select>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php esc_html_e( 'Default timezone', 'moose-booking' ); ?></th>
			<td>
				<?php
				$current_tz = get_option( 'moosebooking_default_timezone', get_option( 'timezone_string', 'Europe/Stockholm' ) );
				?>
				<select name="moosebooking_default_timezone" id="moosebooking_default_timezone">
					<?php echo wp_timezone_choice( $current_tz ); ?>
				</select>
				<p class="description"><?php esc_html_e( 'Select the default timezone for bookings.', 'moose-booking' ); ?></p>

			</td>
		</tr>
	</table>

	<?php submit_button( __( 'Save Settings', 'moose-booking' ), 'primary', 'moosebooking_save_general' ); ?>
</form>
