<!-- E-post & Aviseringar -->

<h2><?php esc_html_e('E-mail', 'moose-booking'); ?></h2>
<form method="post" action="options.php">
    <?php settings_fields('moosebooking_email_settings_group'); ?>

    <table class="form-table">
        <tr>
            <th scope="row"><label for="moosebooking_email_to_admin"><?php esc_html_e('Mottagaradress för bokningar', 'moose-booking'); ?></label></th>
            <td><input type="email" name="moosebooking_email_to_admin" id="moosebooking_email_to_admin" value="<?php echo esc_attr(get_option('moosebooking_email_to_admin')); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th scope="row"><label for="moosebooking_email_message_subject"><?php esc_html_e('Mail-ämnesrad för bokningar', 'moose-booking'); ?></label></th>
            <td><input type="text" name="moosebooking_email_message_subject" id="moosebooking_email_message_subject" value="<?php echo esc_attr(get_option('moosebooking_email_message_subject')); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th scope="row"><label for="moosebooking_email_user_message"><?php esc_html_e('Meddelande till besökare efter bokning', 'moose-booking'); ?></label></th>
            <td>
                <textarea name="moosebooking_email_user_message" id="moosebooking_email_user_message" class="large-text" rows="3"><?php echo esc_attr(get_option('moosebooking_email_user_message')); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Skicka adminbekräftelse vid ny bokning', 'moose-booking'); ?></th>
            <td><input type="checkbox" name="moosebooking_email_notify_admin" value="1" <?php checked(1, get_option('moosebooking_email_notify_admin'), true); ?>></td>
        </tr>
    </table>
    <p><button type="submit" class="button button-primary"><?php esc_html_e('Spara inställningar', 'moose-booking'); ?></button></p>
</form>