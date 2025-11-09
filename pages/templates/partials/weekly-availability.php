<?php
/**
 * Partial template for weekly availability settings.
 *
 * @file pages/templates/partials/weekly-availability.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- ============================================================
		🕒 SECTION 3: Default Weekly Availability
============================================================ -->
<h2><?php esc_html_e( 'Default weekly availability', 'moose-booking' ); ?></h2>
<p><?php esc_html_e( 'Set which weekdays are normally bookable and their default time slots. You can add multiple slots per day.', 'moose-booking' ); ?></p>

<table class="widefat striped moosebooking-weekly-table">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Day', 'moose-booking' ); ?></th>
			<th style="width:90px;"><?php esc_html_e( 'Bookable', 'moose-booking' ); ?></th>
			<th><?php esc_html_e( 'Start–End', 'moose-booking' ); ?></th>
			<th style="width:120px;"><?php esc_html_e( 'Price (kr)', 'moose-booking' ); ?></th>
			<th style="width:120px;"><?php esc_html_e( 'Actions', 'moose-booking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$weekdays = array(
			'mon' => __( 'Monday', 'moose-booking' ),
			'tue' => __( 'Tuesday', 'moose-booking' ),
			'wed' => __( 'Wednesday', 'moose-booking' ),
			'thu' => __( 'Thursday', 'moose-booking' ),
			'fri' => __( 'Friday', 'moose-booking' ),
			'sat' => __( 'Saturday', 'moose-booking' ),
			'sun' => __( 'Sunday', 'moose-booking' ),
		);
			foreach ( $weekdays as $key => $label ) :
			$bookable = ! empty( $weekly_defaults[ $key ]['bookable'] );
			$slots    = $weekly_defaults[ $key ]['slots'] ?? array(
				array( 'start' => '08:00', 'end' => '17:00', 'price' => 0 ),
			);
		?>
		<tr data-day="<?php echo esc_attr( $key ); ?>">
			<th><strong><?php echo esc_html( $label ); ?></strong></th>

			<td>
				<input type="checkbox"
					name="weekly_defaults[<?php echo esc_attr( $key ); ?>][bookable]"
					<?php checked( $bookable ); ?>>
			</td>

			<!-- 🕒 Start–End -->
			<td class="slots-cell">
				<div class="slots-list" data-day="<?php echo esc_attr( $key ); ?>">
					<?php foreach ( $slots as $index => $slot ) : ?>
						<div class="slot-row">
							<input type="time"
								name="weekly_defaults[<?php echo esc_attr( $key ); ?>][slots][<?php echo $index; ?>][start]"
								value="<?php echo esc_attr( $slot['start'] ); ?>">
							<span>–</span>
							<input type="time"
								name="weekly_defaults[<?php echo esc_attr( $key ); ?>][slots][<?php echo $index; ?>][end]"
								value="<?php echo esc_attr( $slot['end'] ); ?>">
							<button type="button"
								class="button-link remove-slot"
								title="<?php esc_attr_e( 'Remove slot', 'moose-booking' ); ?>">×</button>
						</div>
					<?php endforeach; ?>
				</div>
			</td>

			<!-- 💰 Price -->
			<td class="price-cell">
				<div class="slots-list" data-day="<?php echo esc_attr( $key ); ?>-price">
					<?php foreach ( $slots as $index => $slot ) : ?>
						<div class="slot-row">
							<input type="number" step="0.01" min="0"
								name="weekly_defaults[<?php echo esc_attr( $key ); ?>][slots][<?php echo $index; ?>][price]"
								value="<?php echo esc_attr( $slot['price'] ); ?>"
								placeholder="0" style="width:80px;">
						</div>
					<?php endforeach; ?>
				</div>
			</td>

			<!-- ➕ Add Slot -->
			<td>
				<button type="button" class="button add-slot"
					data-day="<?php echo esc_attr( $key ); ?>">
					<?php esc_html_e( 'Add slot', 'moose-booking' ); ?>
				</button>
			</td>
		</tr>

		<?php endforeach; ?>
	</tbody>
</table>
