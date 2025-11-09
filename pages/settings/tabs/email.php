<?php
/**
 * Email settings tab.
 *
 * @file pages/settings/tabs/email.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// Handle form submission.
if ( isset( $_POST['moosebooking_save_email'] ) && check_admin_referer( 'moosebooking_save_email_nonce' ) ) {

	update_option( 'moosebooking_email_to_admin', sanitize_email( wp_unslash( $_POST['moosebooking_email_to_admin'] ?? '' ) ) );
	update_option( 'moosebooking_email_message_subject', sanitize_text_field( wp_unslash( $_POST['moosebooking_email_message_subject'] ?? '' ) ) );
	update_option( 'moosebooking_email_user_message', sanitize_textarea_field( wp_unslash( $_POST['moosebooking_email_user_message'] ?? '' ) ) );
	update_option( 'moosebooking_email_notify_admin', isset( $_POST['moosebooking_email_notify_admin'] ) ? 1 : 0 );

	echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings saved.', 'moose-booking' ) . '</p></div>';
}
?>

<h2><?php esc_html_e( 'E-mail', 'moose-booking' ); ?></h2>

<form method="post">
	<?php wp_nonce_field( 'moosebooking_save_email_nonce' ); ?>

	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<label for="moosebooking_email_to_admin"><?php esc_html_e( 'Mottagaradress för bokningar', 'moose-booking' ); ?></label>
			</th>
			<td>
				<input type="email"
					name="moosebooking_email_to_admin"
					id="moosebooking_email_to_admin"
					value="<?php echo esc_attr( get_option( 'moosebooking_email_to_admin', get_option( 'admin_email' ) ) ); ?>"
					class="regular-text" />
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="moosebooking_email_message_subject"><?php esc_html_e( 'Mail-ämnesrad för bokningar', 'moose-booking' ); ?></label>
			</th>
			<td>
				<input type="text"
					name="moosebooking_email_message_subject"
					id="moosebooking_email_message_subject"
					value="<?php echo esc_attr( get_option( 'moosebooking_email_message_subject', __( 'Ny bokning mottagen', 'moose-booking' ) ) ); ?>"
					class="regular-text" />
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="moosebooking_email_user_message"><?php esc_html_e( 'Meddelande till besökare efter bokning', 'moose-booking' ); ?></label>
			</th>
			<td>
				<textarea name="moosebooking_email_user_message"
					id="moosebooking_email_user_message"
					class="large-text"
					rows="4"><?php echo esc_textarea( get_option( 'moosebooking_email_user_message', __( 'Tack för din bokning! Vi återkommer vid behov.', 'moose-booking' ) ) ); ?></textarea>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Skicka adminbekräftelse vid ny bokning', 'moose-booking' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="moosebooking_email_notify_admin" value="1"
						<?php checked( 1, get_option( 'moosebooking_email_notify_admin' ) ); ?> />
					<?php esc_html_e( 'Ja, skicka e-post till administratören', 'moose-booking' ); ?>
				</label>
			</td>
		</tr>
	</table>

	<?php submit_button( __( 'Save settings', 'moose-booking' ), 'primary', 'moosebooking_save_email' ); ?>
</form>
