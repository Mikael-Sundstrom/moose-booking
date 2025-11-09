<?php
/**
 * Template editor page.
 *
 * @file pages/templates/template-editor.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

global $wpdb;

$template_id          = isset( $_GET['template_id'] ) ? intval( $_GET['template_id'] ) : 0;
$template_name        = '';
$template_description = '';
$active               = 1;
$custom_dates         = array();
$weekly_defaults      = array();
$unbookable_dates     = array();
$unbookable_weeks     = array();
$max_days_ahead       = 30;
$min_hours_before     = 0;
$override_limits      = 0;

$table = $wpdb->prefix . 'moosebooking_templates';

/**
 * ============================================================
 * 🌍 Global defaults
 * ============================================================
 */
$global_max_days_ahead   = get_option( 'moosebooking_limits_max_days_ahead', 30 );
$global_min_hours_before = get_option( 'moosebooking_limits_min_hours_before_booking', 0 );

/**
 * ============================================================
 * 🔄 Load existing template if ID is provided
 * ============================================================
 */
if ( $template_id ) {
	$template = $wpdb->get_row(
		$wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $template_id )
	);

	if ( $template ) {
		$template_name        = $template->name;
		$template_description = $template->description;
		$active               = (int) $template->active;
		$custom_dates         = json_decode( $template->custom_dates ?? '[]', true );
		$weekly_defaults      = json_decode( $template->weekly_defaults ?? '{}', true );
		$unbookable_dates     = json_decode( $template->unbookable_dates ?? '[]', true );
		$unbookable_weeks     = json_decode( $template->unbookable_weeks ?? '[]', true );

		$max_days_ahead       = intval( $template->max_days_ahead ?? $global_max_days_ahead );
		$min_hours_before     = intval( $template->min_hours_before ?? $global_min_hours_before );
		$override_limits      = intval( $template->override_limits ?? 0 );
	} else {
		wp_die( esc_html__( 'Template not found.', 'moose-booking' ) );
	}
}
?>

<div class="wrap">
  <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
    <?php wp_nonce_field( 'moosebooking_save_template', 'moosebooking_template_nonce' ); ?>
    <input type="hidden" name="action" value="moosebooking_save_template">
    <input type="hidden" id="template_id" name="template_id" value="<?php echo esc_attr( $template_id ); ?>">
    <input type="hidden" id="weekly_defaults_json" name="weekly_defaults_json"
      value="<?php echo esc_attr( wp_json_encode( $weekly_defaults ) ); ?>">
	<input type="hidden" id="unbookable_dates_input" name="unbookable_dates"
      value="<?php echo esc_attr( wp_json_encode( $unbookable_dates ) ); ?>">
	<input type="hidden" id="unbookable_weeks_input" name="unbookable_weeks"
      value="<?php echo esc_attr( wp_json_encode( $unbookable_weeks ) ); ?>">

    <h1>
      <?php echo esc_html( $template_id ? __( 'Edit booking template', 'moose-booking' ) : __( 'Create new booking template', 'moose-booking' ) ); ?>
    </h1>

    <!-- 📄 Template Meta -->
    <div class="template-meta">
      <p>
        <label for="template_name"><?php esc_html_e( 'Template name', 'moose-booking' ); ?></label><br>
        <input type="text" id="template_name" name="template_name"
          value="<?php echo esc_attr( $template_name ); ?>" required>
      </p>

      <p>
        <label for="template_description"><?php esc_html_e( 'Description', 'moose-booking' ); ?></label><br>
        <input type="text" id="template_description" name="template_description"
          value="<?php echo esc_attr( $template_description ); ?>" maxlength="255" style="width: 100%;">
      </p>

      <p>
        <label>
          <input type="checkbox" name="active" value="1" <?php checked( $active, 1 ); ?>>
          <?php esc_html_e( 'Active', 'moose-booking' ); ?>
        </label>
      </p>
    </div>

    <hr>

    <!-- 🕒 Default Weekly Availability -->
    <?php include_once __DIR__ . '/partials/weekly-availability.php'; ?>

    <hr>

    <!-- 📆 Booking limits -->
	<h2><?php esc_html_e( 'Booking limits', 'moose-booking' ); ?></h2>
	<p class="text-muted">
		<?php esc_html_e( 'Specify if this template should override the global booking limits.', 'moose-booking' ); ?>
	</p>

	<table class="form-table moosebooking-booking-range">
		<tr>
			<th scope="row">
				<label for="override_limits"><?php esc_html_e( 'Use custom limits for this template', 'moose-booking' ); ?></label>
			</th>
			<td>
				<label>
					<input type="checkbox" id="override_limits" name="override_limits" value="1" <?php checked( 1, $override_limits ); ?> />
					<?php esc_html_e( 'Enable custom rules instead of global defaults', 'moose-booking' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="max_days_ahead"><?php esc_html_e( 'Maximum days ahead', 'moose-booking' ); ?></label>
			</th>
			<td>
				<input type="number" id="max_days_ahead" name="max_days_ahead"
					value="<?php echo esc_attr( $override_limits ? $max_days_ahead : $global_max_days_ahead ); ?>"
					min="1" max="730" step="1"
					data-global-value="<?php echo esc_attr( $global_max_days_ahead ); ?>"
					data-local-value="<?php echo esc_attr( $max_days_ahead ); ?>" />
				<p class="description">
					<?php
					printf(
						esc_html__( 'Number of days ahead users can book. Global default: %d', 'moose-booking' ),
						intval( $global_max_days_ahead )
					);
					?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="min_hours_before"><?php esc_html_e( 'Minimum hours before booking', 'moose-booking' ); ?></label>
			</th>
			<td>
				<input type="number" id="min_hours_before" name="min_hours_before"
					value="<?php echo esc_attr( $override_limits ? $min_hours_before : $global_min_hours_before ); ?>"
					min="0" max="168" step="1"
					data-global-value="<?php echo esc_attr( $global_min_hours_before ); ?>"
					data-local-value="<?php echo esc_attr( $min_hours_before ); ?>" />
				<p class="description">
					<?php
					printf(
						esc_html__( 'How many hours before start a booking must be made. Global default: %d', 'moose-booking' ),
						intval( $global_min_hours_before )
					);
					?>
				</p>
			</td>
		</tr>
	</table>

	<hr>

	<!-- 🚫 Unavailable Periods -->
	<h2><?php esc_html_e( 'Unavailable periods', 'moose-booking' ); ?></h2>

	<table class="form-table">
	<tr>
		<th><label for="unbookable_dates_text"><?php esc_html_e( 'Unavailable dates (MM-DD)', 'moose-booking' ); ?></label></th>
		<td>
		<input type="text" id="unbookable_dates_text" name="unbookable_dates_text"
			value="<?php echo esc_attr( implode( ', ', $unbookable_dates ) ); ?>"
			placeholder="12-24, 12-25, 12-31" style="width: 100%;">
		<p class="description"><?php esc_html_e( 'Enter recurring blocked dates each year, separated by commas.', 'moose-booking' ); ?></p>
		</td>
	</tr>

	<tr>
		<th><label for="unbookable_weeks_text"><?php esc_html_e( 'Unavailable weeks (ISO week numbers)', 'moose-booking' ); ?></label></th>
		<td>
		<input type="text" id="unbookable_weeks_text" name="unbookable_weeks_text"
			value="<?php echo esc_attr( implode( ', ', $unbookable_weeks ) ); ?>"
			placeholder="29, 30, 31" style="width: 100%;">
		<p class="description"><?php esc_html_e( 'Enter week numbers that are blocked every year.', 'moose-booking' ); ?></p>
		</td>
	</tr>
	</table>

	<hr>

    <!-- 💾 Save / Cancel Buttons -->
    <p class="submit">
      <button type="submit" class="button button-primary">
        <?php esc_html_e( 'Save template', 'moose-booking' ); ?>
      </button>
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=moosebooking-templates' ) ); ?>" class="button">
        <?php esc_html_e( 'Cancel', 'moose-booking' ); ?>
      </a>
    </p>
  </form>
</div>
