<h2><?php esc_html_e('Begränsningar', 'moose-booking'); ?></h2>

<form method="post" action="options.php">
    <?php settings_fields('moosebooking_limits_settings_group'); ?>
    
    <table class="form-table">
        <tr>
            <th scope="row"><label for="max_bookings_per_ip"><?php esc_html_e('Antal bokningar per IP per dag', 'moose-booking'); ?></label></th>
            <td><input type="number" name="moosebooking_limits_max_bookings_per_ip" id="max_bookings_per_ip" min="0" value="<?php echo esc_attr(get_option('moosebooking_limits_max_bookings_per_ip')); ?>" class="small-text"></td>
        </tr>
        <tr>
            <th scope="row"><label for="min_hours_before_booking"><?php esc_html_e('Minst antal timmar innan bokning får göras', 'moose-booking'); ?></label></th>
            <td><input type="number" name="moosebooking_limits_min_hours_before_booking" id="min_hours_before_booking" min="0" value="<?php echo esc_attr(get_option('moosebooking_limits_min_hours_before_booking')); ?>" class="small-text"></td>
        </tr>
        <tr>
            <th scope="row"><label for="moosebooking_limits_max_days_ahead"><?php esc_html_e('Max antal dagar i framtiden man kan boka', 'moose-booking'); ?></label></th>
            <td><input type="number" name="moosebooking_limits_max_days_ahead" id="moosebooking_limits_max_days_ahead" min="0" value="<?php echo esc_attr(get_option('moosebooking_limits_max_days_ahead')); ?>" class="small-text"></td>
        </tr>
    </table>

    <p><button type="submit" class="button button-primary"><?php esc_html_e('Spara inställningar', 'moose-booking'); ?></button></p>
</form>
