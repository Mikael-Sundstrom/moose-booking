<h2><?php esc_html_e('Utseende', 'moose-booking'); ?></h2>

<form method="post" action="options.php">
    <?php settings_fields('moosebooking_appearance_settings_group'); ?>
    
    <table class="form-table">
        <tr>
            <th scope="row"><label for="moosebooking_appearance_primary_color"><?php esc_html_e('Primär färg på kalender/formulär', 'moose-booking'); ?></label></th>
            <td><input type="color" name="moosebooking_appearance_primary_color" id="moosebooking_appearance_primary_color" value="<?php echo esc_attr(get_option('moosebooking_appearance_primary_color')); ?>"></td>
        </tr>
        <tr>
            <th scope="row"><label for="moosebooking_appearance_calendar_view"><?php esc_html_e('Visningsläge för kalender', 'moose-booking'); ?></label></th>
            <?php $current = get_option('moosebooking_appearance_calendar_view'); ?>
            <td>
                <select name="moosebooking_appearance_calendar_view" id="moosebooking_appearance_calendar_view">
                    <option value="grid" <?php selected($current, 'grid'); ?>>
                        <?php esc_html_e('Grid', 'moose-booking'); ?>
                    </option>
                    <option value="list" <?php selected($current, 'list'); ?>>
                        <?php esc_html_e('List', 'moose-booking'); ?>
                    </option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Visa veckonummer', 'moose-booking'); ?></th>
            <td><input type="checkbox" name="moosebooking_appearance_show_week_numbers" value="1" <?php checked(1, get_option('moosebooking_appearance_show_week_numbers'), true); ?>></td>
        </tr>
    </table>

    <p><button type="submit" class="button button-primary"><?php esc_html_e('Spara inställningar', 'moose-booking'); ?></button></p>
</form>