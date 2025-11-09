<?php
/**
 * Settings tab for appearance.
 *
 * @file pages/settings/tabs/appearance.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

// Handle form submission manually.
if ( isset( $_POST['moosebooking_save_appearance'] ) && check_admin_referer( 'moosebooking_save_appearance_nonce' ) ) {

	update_option(
		'moosebooking_appearance_primary_color',
		sanitize_hex_color( wp_unslash( $_POST['moosebooking_appearance_primary_color'] ?? '#0073aa' ) )
	);

	update_option(
		'moosebooking_appearance_calendar_view',
		in_array( $_POST['moosebooking_appearance_calendar_view'] ?? 'grid', array( 'grid', 'list' ), true ) ? $_POST['moosebooking_appearance_calendar_view'] : 'grid'
	);

	update_option(
		'moosebooking_appearance_show_week_numbers',
		isset( $_POST['moosebooking_appearance_show_week_numbers'] ) ? 1 : 0
	);

	echo '<div class="updated notice is-dismissible"><p>' .
		esc_html__( 'Appearance settings saved.', 'moose-booking' ) .
		'</p></div>';
}

// Retrieve saved options.
$primary_color   = get_option( 'moosebooking_appearance_primary_color', '#0073aa' );
$calendar_view   = get_option( 'moosebooking_appearance_calendar_view', 'grid' );
$show_weeknums   = get_option( 'moosebooking_appearance_show_week_numbers', 0 );
?>

<h2><?php esc_html_e( 'Utseende', 'moose-booking' ); ?></h2>

<form method="post">
	<?php wp_nonce_field( 'moosebooking_save_appearance_nonce' ); ?>

	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<label for="moosebooking_appearance_primary_color">
					<?php esc_html_e( 'Primär färg på kalender och formulär', 'moose-booking' ); ?>
				</label>
			</th>
			<td>
				<input type="color"
					name="moosebooking_appearance_primary_color"
					id="moosebooking_appearance_primary_color"
					value="<?php echo esc_attr( $primary_color ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Denna färg används som accent i kalendern och bokningsformulär.', 'moose-booking' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="moosebooking_appearance_calendar_view">
					<?php esc_html_e( 'Standardvisning för kalendern', 'moose-booking' ); ?>
				</label>
			</th>
			<td>
				<select name="moosebooking_appearance_calendar_view" id="moosebooking_appearance_calendar_view">
					<option value="grid" <?php selected( $calendar_view, 'grid' ); ?>>
						<?php esc_html_e( 'Rutnät (månadsvy)', 'moose-booking' ); ?>
					</option>
					<option value="list" <?php selected( $calendar_view, 'list' ); ?>>
						<?php esc_html_e( 'Lista (kommande bokningar)', 'moose-booking' ); ?>
					</option>
				</select>
				<p class="description">
					<?php esc_html_e( 'Välj hur kalendern visas som standard för besökare.', 'moose-booking' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Visa veckonummer', 'moose-booking' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox"
						name="moosebooking_appearance_show_week_numbers"
						value="1"
						<?php checked( 1, $show_weeknums ); ?> />
					<?php esc_html_e( 'Aktivera visning av veckonummer i kalendern', 'moose-booking' ); ?>
				</label>
			</td>
		</tr>
	</table>

	<?php submit_button( __( 'Spara utseendeinställningar', 'moose-booking' ), 'primary', 'moosebooking_save_appearance' ); ?>
</form>
