<?php if (!defined('ABSPATH')) exit; 

// Om admin tryckt på "Rensa alla inställningar"
if (isset($_POST['moosebooking_reset_options']) && check_admin_referer('moosebooking_reset_options_nonce')) {
    global $wpdb;
    $options = $wpdb->get_col(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'moosebooking_%'"
    );
    foreach ($options as $option_name) {
        delete_option($option_name);
    }
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success"><p>Alla MooseBooking-inställningar har raderats.</p></div>';
    });
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <hr>
<h2><?php esc_html_e('Återställ inställningar', 'moose-booking'); ?></h2>
<p><?php esc_html_e('Om du vill rensa alla sparade Moose Booking-inställningar och börja om, klicka på knappen nedan.', 'moose-booking'); ?></p>

<form method="post">
    <?php wp_nonce_field('moosebooking_reset_options_nonce'); ?>
    <button type="submit" name="moosebooking_reset_options" class="button button-danger" onclick="return confirm('Är du säker på att du vill ta bort alla inställningar?');">
        <?php esc_html_e('Rensa alla inställningar', 'moose-booking'); ?>
    </button>
</form>


    <h2><?php esc_html_e('Saved Plugin Options', 'moose-booking'); ?></h2>

    <p><?php esc_html_e('Below are the current option values saved by Moose Booking.', 'moose-booking'); ?></p>

    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Option Name', 'moose-booking'); ?></th>
                <th><?php esc_html_e('Value', 'moose-booking'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            // Hämta alla alternativ som börjar med moosebooking_
            $options = $wpdb->get_results(
                "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'moosebooking_%'"
            );

            if ($options) :
                foreach ($options as $option) :
                    ?>
                    <tr>
                        <td><?php echo esc_html($option->option_name); ?></td>
                        <td>
                            <code>
                                <?php 
                                // Om det är en array eller objekt, visa snyggare
                                if (is_serialized($option->option_value)) {
                                    $value = maybe_unserialize($option->option_value);
                                    echo '<pre>' . esc_html(print_r($value, true)) . '</pre>';
                                } else {
                                    echo esc_html($option->option_value);
                                }
                                ?>
                            </code>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else :
                ?>
                <tr>
                    <td colspan="2"><?php esc_html_e('No options found.', 'moose-booking'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
