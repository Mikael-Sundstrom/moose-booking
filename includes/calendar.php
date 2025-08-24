<?php if (!defined('ABSPATH')) exit;

/**
 * Renderar kalendern för en viss månad och år.
 *
 * @param int $year  Årtal
 * @param int $month Månad (1-12)
 * @param array $custom_dates Array av datumsträngar eller objekt med detaljer.
 *
 * @return string HTML för kalendern.
 */
function moosebooking_render_calendar($year, $month, $custom_dates = []) {

    // Gör om custom_dates till en snabb lookup-array om det är ett objekt
    $custom_date_lookup = [];
    foreach ($custom_dates as $date_info) {
        if (is_array($date_info) && isset($date_info['date'])) {
            $custom_date_lookup[$date_info['date']] = $date_info;
        } elseif (is_string($date_info)) {
            $custom_date_lookup[$date_info] = ['bookable' => false];
        }
    }

    $days_in_month = date('t', strtotime("$year-$month-01"));
    $start_of_week = get_option('start_of_week');
    $first_day = (date('w', strtotime("$year-$month-01")) - $start_of_week + 7) % 7;
    $current_week = date('W', strtotime("$year-$month-01"));
    $day_counter = 1;
    $empty_days = $first_day;
    $total_cells = ceil(($empty_days + $days_in_month) / 7) * 7;

    ob_start();
    ?>
    <div class="weekday">Week</div>
    <?php
    $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $adjusted_weekdays = array_merge(
        array_slice($weekdays, $start_of_week),
        array_slice($weekdays, 0, $start_of_week)
    );

    foreach ($adjusted_weekdays as $dayname) {
        echo '<div class="weekday">' . esc_html($dayname) . '</div>';
    }

    for ($cell = 0; $cell < $total_cells; $cell++) {
        if ($cell % 7 === 0) {
            echo '<div class="weeknumber">' . $current_week . '</div>';
            $current_week++;
        }
        if ($cell < $empty_days) {
            echo '<div class="day empty"></div>';
        } elseif ($day_counter <= $days_in_month) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day_counter);

            $classes = ['day'];
            if (isset($custom_date_lookup[$date])) {
                $classes[] = 'custom-day';
                if ($custom_date_lookup[$date]['bookable'] == false) {

                    $classes[] = 'unavailable';
                }
            }

            echo '<div class="' . esc_attr(implode(' ', $classes)) . '" data-date="' . esc_attr($date) . '">' . $day_counter . '</div>';
            $day_counter++;
        } else {
            echo '<div class="day empty"></div>';
        }
    }

    return ob_get_clean();
}
