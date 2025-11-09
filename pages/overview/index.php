<?php
/**
 * Admin overview page.
 *
 * @file pages/overview/index.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<p><?php esc_html_e( 'Välkommen till Moose Booking! Här får du en snabb överblick över bokningssystemets status.', 'moose-booking' ); ?></p>

	<hr>

	<h2><?php esc_html_e( 'Snabbstatistik', 'moose-booking' ); ?></h2>
	<div style="display: flex; gap: 20px; margin-top: 20px;">
		<div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; flex: 1; text-align: center;">
			<h3><?php esc_html_e( 'Totalt antal formulär', 'moose-booking' ); ?></h3>
			<p><strong><?php echo esc_html( '0' ); ?></strong></p>
		</div>
		<div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; flex: 1; text-align: center;">
			<h3><?php esc_html_e( 'Totalt antal bokningar', 'moose-booking' ); ?></h3>
			<p><strong><?php echo esc_html( '0' ); ?></strong></p>
		</div>
		<div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; flex: 1; text-align: center;">
			<h3><?php esc_html_e( 'SMS-kassa', 'moose-booking' ); ?></h3>
			<p><strong><?php echo esc_html( '0' ); ?> kr</strong></p>
		</div>
	</div>

	<hr>

	<h2><?php esc_html_e( 'Senaste bokningar', 'moose-booking' ); ?></h2>
	<p><?php esc_html_e( 'Här visas de senaste inkomna bokningarna.', 'moose-booking' ); ?></p>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Datum', 'moose-booking' ); ?></th>
				<th><?php esc_html_e( 'Formulär', 'moose-booking' ); ?></th>
				<th><?php esc_html_e( 'Namn', 'moose-booking' ); ?></th>
				<th><?php esc_html_e( 'E-post', 'moose-booking' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4"><?php esc_html_e( 'Inga bokningar än.', 'moose-booking' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
