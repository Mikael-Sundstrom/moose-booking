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

        
        <div class="calendar-header">
            <button type="button" id="prev-month">&laquo;</button>
            <span id="calendar-month-year"></span>
            <button type="button" id="next-month">&raquo;</button>
        </div>
        
        <div class="moosebooking-calendar">
            <?php 
            $year = date('Y');
            $month = date('n');
            
            echo moosebooking_render_calendar($year, $month, $custom_dates);
            ?>
        </div>

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

        <div id="custom-day-modal">
            <div class="modal-content">
                <h2>Edit Day</h2>
                <p>Date: <span id="custom-day-date"></span></p>

                <label>
                    <input type="checkbox" id="custom-day-bookable"> Bookable
                </label>

                <h3>Available Times</h3>
                <div id="custom-day-times"></div>

                <button type="button" id="save-custom-day">Save</button>
                <button type="button" id="close-custom-day">Close</button>
            </div>
        </div>

    </form>
</div>
