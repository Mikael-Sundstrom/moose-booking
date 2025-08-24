<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <p><?php esc_html_e('Here you can create and manage booking templates.', 'moose-booking'); ?></p>

    <p>
        <a href="<?php echo admin_url('admin.php?page=moosebooking-template-editor'); ?>" class="button button-primary">
            <?php esc_html_e('Create new template', 'moose-booking'); ?>
        </a>
    </p>

    <hr>

    <h2><?php esc_html_e('Available templates', 'moose-booking'); ?></h2>

    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Template name', 'moose-booking'); ?></th>
                <th><?php esc_html_e('Booking type', 'moose-booking'); ?></th>
                <th><?php esc_html_e('Status', 'moose-booking'); ?></th>
                <th><?php esc_html_e('Actions', 'moose-booking'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'moosebooking_templates';

            // Hämta mallar från databasen
            $templates = $wpdb->get_results(
                "SELECT id, name, description, active FROM $table_name",
                ARRAY_A
            );

            if (!empty($templates)) :
                foreach ($templates as $template) :
                    ?>
                    <tr>
                        <td><?php echo esc_html($template['name']); ?></td>
                        <td><?php echo esc_html($template['description']); ?></td>
                        <td>
                            <?php if (!empty($template['active'])) : ?>
                                <span style="color: green;"><?php esc_html_e('Active', 'moose-booking'); ?></span>
                            <?php else : ?>
                                <span style="color: red;"><?php esc_html_e('Inactive', 'moose-booking'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=moosebooking-template-editor&template_id=' . intval($template['id'])); ?>">
                                <?php esc_html_e('Edit', 'moose-booking'); ?>
                            </a> |
                            <a href="#" onclick="return confirm('Are you sure you want to delete this template?');">
                                <?php esc_html_e('Delete', 'moose-booking'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else :
                ?>
                <tr>
                    <td colspan="5"><?php esc_html_e('No booking templates created yet.', 'moose-booking'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
