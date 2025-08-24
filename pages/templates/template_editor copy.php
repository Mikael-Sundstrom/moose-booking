<?php if (!defined('ABSPATH')) exit;

// Få template ID från URL
$template_id = isset($_GET['template_id']) ? intval($_GET['template_id']) : 0;

$template_name = '';
$template_description = '';
$active = 1;
$time_slots = [];
$custom_dates = [];
$pricing = [];

global $wpdb;
$table = $wpdb->prefix . 'moosebooking_templates';

// Hämta från databas om template_id finns
if ($template_id) {
    $template = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $table WHERE id = %d", $template_id
    ) );

    if ($template) {
        $template_name = $template->name;
        $template_description = $template->description;
        $active = (int) $template->active;
        $time_slots = json_decode($template->time_slots, true) ?: [];
        $custom_dates = json_decode($template->custom_dates, true) ?: [];
        $pricing = json_decode($template->pricing, true) ?: [];
    }
}
?>

<div class="wrap">
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <h1><?php echo esc_html( $template_id ? __('Edit booking template', 'moose-booking') : __('Create new booking template', 'moose-booking') ); ?></h1>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="template_name"><?php esc_html_e('Name *', 'moose-booking'); ?></label></th>
                <td><input name="template_name" type="text" id="template_name" value="<?php echo esc_attr($template_name); ?>" class="regular-text" required></td>
            </tr>

            <tr>
                <th scope="row"><label for="template_description"><?php esc_html_e('Description', 'moose-booking'); ?></label></th>
                <td><input name="template_description" type="text" id="template_description" value="<?php echo esc_attr($template_description); ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e('Active', 'moose-booking'); ?></th>
                <td>
                    <label><input type="radio" name="active" value="1" <?php checked($active, 1); ?>> <?php esc_html_e('Yes', 'moose-booking'); ?></label><br>
                    <label><input type="radio" name="active" value="0" <?php checked($active, 0); ?>> <?php esc_html_e('No', 'moose-booking'); ?></label>
                </td>
            </tr>
        </table>

        <hr><br>

        <h2><?php esc_html_e('Standard available time slots', 'moose-booking'); ?></h2>
        <div id="moosebooking-time-slots">
            <p><button type="button" id="add-time-slot" class="button">Add time slot</button></p>
            <br>
            <?php if (!empty($time_slots)): ?>
                <?php foreach ($time_slots as $slot): ?>
                    <div class="time-slot">
                        <label><?php esc_html_e('Start time:', 'moose-booking'); ?></label>
                        <input type="time" name="standard_start_times[]" value="<?php echo esc_attr($slot['start'] ?? ''); ?>">
                        <label><?php esc_html_e('End time:', 'moose-booking'); ?></label>
                        <input type="time" name="standard_end_times[]" value="<?php echo esc_attr($slot['end'] ?? ''); ?>">
                        <button type="button" class="remove-slot button">×</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="time-slot">
                    <label><?php esc_html_e('Start time:', 'moose-booking'); ?></label>
                    <input type="time" name="standard_start_times[]" value="">
                    <label><?php esc_html_e('End time:', 'moose-booking'); ?></label>
                    <input type="time" name="standard_end_times[]" value="">
                    <button type="button" class="remove-slot button">×</button>
                </div>
            <?php endif; ?>
        </div>

        <br><hr><br>

        <h2><?php esc_html_e('Pricing', 'moose-booking'); ?></h2>
        <div id="moosebooking-pricing-slots">
            <p><button type="button" id="add-pricing-slot" class="button">Add price</button></p>
            <br>
            <?php if (!empty($pricing)): ?>
                <?php foreach ($pricing as $price): ?>
                    <div class="pricing-slot">
                        <label>Type:</label>
                        <select name="pricing_type[]">
                            <?php
                            $types = ['hourly','daily','weekend','weekly','custom'];
                            foreach ($types as $type) {
                                echo '<option value="' . esc_attr($type) . '" ' . selected($price['type'] ?? '', $type, false) . '>' . ucfirst($type) . '</option>';
                            }
                            ?>
                        </select>
                        <label>Price:</label>
                        <input type="number" name="pricing_amount[]" step="0.01" value="<?php echo esc_attr($price['amount'] ?? ''); ?>">
                        <label>Comment:</label>
                        <input type="text" name="pricing_comment[]" value="<?php echo esc_attr($price['comment'] ?? ''); ?>">
                        <button type="button" class="remove-pricing-slot button">×</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="pricing-slot">
                    <label>Type:</label>
                    <select name="pricing_type[]">
                        <option value="hourly">Hourly</option>
                        <option value="daily">Daily</option>
                        <option value="weekend">Weekend</option>
                        <option value="weekly">Weekly</option>
                        <option value="custom">Custom</option>
                    </select>
                    <label>Price:</label>
                    <input type="number" name="pricing_amount[]" step="0.01" value="">
                    <label>Comment:</label>
                    <input type="text" name="pricing_comment[]" value="">
                    <button type="button" class="remove-pricing-slot button">×</button>
                </div>
            <?php endif; ?>
        </div>

        <br><hr><br>

        <?php 
        $year = date('Y');
        $month = date('n');
        
        echo moosebooking_render_calendar($year, $month, $custom_dates);
        ?>
        
        <br>
        <input id="custom_dates_input" name="custom_dates" value="<?php echo esc_attr(json_encode($custom_dates)); ?>">
        <!-- <input type="hidden" id="custom_dates_input" name="custom_dates" value="<?php //echo esc_attr(json_encode($custom_dates)); ?>"> -->

        <p>
            <button type="submit" class="button button-primary"><?php esc_html_e('Save template', 'moose-booking'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=moosebooking-templates'); ?>" class="button"><?php esc_html_e('Cancel', 'moose-booking'); ?></a>
        </p>

        <?php wp_nonce_field('moosebooking_save_template', 'moosebooking_template_nonce'); ?>
        <input type="hidden" name="action" value="moosebooking_save_template">
        <input type="hidden" id="template_id" name="template_id" value="<?php echo esc_attr($template_id); ?>">

        <div id="custom-day-modal" style="display:none;">
            <h2>Edit Day</h2>
            <p>Date: <span id="custom-day-date"></span></p>

            <label>
                <input type="checkbox" id="custom-day-bookable"> Bookable
            </label>

            <h3>Available Times</h3>
            <div id="custom-day-times"></div>
            <button type="button" id="add-time-range">Add time slot</button>

            <button type="button" id="save-custom-day">Save</button>
            <button type="button" id="close-custom-day">Close</button>
        </div>

    </form>
</div>
