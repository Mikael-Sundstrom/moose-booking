<?php
/**
 * Calendar functions for displaying the booking calendar.
 *
 * @file includes/calendar.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the calendar for a specific month and year.
 *
 * @param int $year  Year
 * @param int $month Month (1-12)
 * @param array $custom_dates Array of date strings or objects with details.
 *
 * @return string HTML for the calendar.
 */
function moosebooking_render_calendar( $year, $month, $custom_dates = array() ) {

	// Convert custom_dates to a quick lookup array if it's an object array.
	$custom_date_lookup = array();
	foreach ( $custom_dates as $date_info ) {
		if ( is_array( $date_info ) && isset( $date_info['date'] ) ) {
			$custom_date_lookup[ $date_info['date'] ] = $date_info;
		} elseif ( is_string( $date_info ) ) {
			$custom_date_lookup[ $date_info ] = array( 'bookable' => false );
		}
	}

	$days_in_month = date( 't', strtotime( "$year-$month-01" ) );
	$start_of_week = get_option( 'start_of_week' );
	$first_day     = ( date( 'w', strtotime( "$year-$month-01" ) ) - $start_of_week + 7 ) % 7;
	$current_week  = date( 'W', strtotime( "$year-$month-01" ) );
	$day_counter   = 1;
	$empty_days    = $first_day;
	$total_cells   = ceil( ( $empty_days + $days_in_month ) / 7 ) * 7;

	ob_start();
	?>
	<div class="weekday">Week</div>
	<?php
	$weekdays          = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
	$adjusted_weekdays = array_merge(
		array_slice( $weekdays, $start_of_week ),
		array_slice( $weekdays, 0, $start_of_week )
	);

	foreach ( $adjusted_weekdays as $dayname ) {
		echo '<div class="weekday">' . esc_html( $dayname ) . '</div>';
	}

	for ( $cell = 0; $cell < $total_cells; $cell++ ) {
		if ( 0 === $cell % 7 ) {
			echo '<div class="weeknumber">' . $current_week . '</div>';
			++$current_week;
		}
		if ( $cell < $empty_days ) {
			echo '<div class="day empty"></div>';
		} elseif ( $day_counter <= $days_in_month ) {
			$date = sprintf( '%04d-%02d-%02d', $year, $month, $day_counter );

			$classes = array( 'day' );
			if ( isset( $custom_date_lookup[ $date ] ) ) {
				$classes[] = 'custom-day';
				if ( false === $custom_date_lookup[ $date ]['bookable'] ) {

					$classes[] = 'unavailable';
				}
			}

			echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-date="' . esc_attr( $date ) . '">' . $day_counter . '</div>';
			++$day_counter;
		} else {
			echo '<div class="day empty"></div>';
		}
	}

	return ob_get_clean();
}
