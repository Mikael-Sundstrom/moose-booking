<?php if (!defined('ABSPATH')) exit;

/**
 * template-editor-handler.php
 * Hanterar sparning och radering av bokningsmallar
 */

// Spara mall
add_action('admin_post_moosebooking_save_template', 'moosebooking_handle_save_template');

// Radera mall (om du lägger till delete-funktion senare)
add_action('admin_post_moosebooking_delete_template', 'moosebooking_handle_delete_template');

/**
 * Spara eller uppdatera mall
 */
function moosebooking_handle_save_template() {

    // Säkerhetskontroller
    if (!isset($_POST['moosebooking_template_nonce']) ||
        !wp_verify_nonce($_POST['moosebooking_template_nonce'], 'moosebooking_save_template')) {
        wp_die(__('Security check failed.', 'moose-booking'));
    }

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to perform this action.', 'moose-booking'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'moosebooking_templates';

    $template_id = isset($_POST['template_id']) ? absint($_POST['template_id']) : 0;
    $name = moosebooking_sanitize_input($_POST['template_name'] ?? '');
    $description = moosebooking_sanitize_input($_POST['template_description'] ?? '', 'textarea');
    $active = moosebooking_sanitize_input($_POST['active'] ?? 0, 'number');

    // ===== TIME SLOTS =====
    $time_slots = [];
    $start_times = $_POST['standard_start_times'] ?? [];
    $end_times = $_POST['standard_end_times'] ?? [];

    foreach ($start_times as $index => $start) {
        // Hoppa över tomma slots
        if (empty($start) && empty($end_times[$index])) continue;

        $time_slots[] = [
            'start' => moosebooking_sanitize_input($start),
            'end' => moosebooking_sanitize_input($end_times[$index] ?? '')
        ];
    }

    // ===== PRICING =====
    $pricing = [];
    $types = $_POST['pricing_type'] ?? [];
    $amounts = $_POST['pricing_amount'] ?? [];
    $comments = $_POST['pricing_comment'] ?? [];

    foreach ($types as $index => $type) {
        if (empty($type) && empty($amounts[$index])) continue; // Hoppa över tomma

        $pricing[] = [
            'type' => moosebooking_sanitize_input($type),
            'amount' => floatval($amounts[$index] ?? 0),
            'comment' => moosebooking_sanitize_input($comments[$index] ?? '')
        ];
    }

    // ===== CUSTOM DATES =====
    $custom_dates_raw = stripslashes($_POST['custom_dates'] ?? '');
    $custom_dates = json_decode($custom_dates_raw, true);

    if (!is_array($custom_dates)) {
        $custom_dates = [];
    }

    // Rensa och sanera varje datum
    $cleaned_custom_dates = [];
    foreach ($custom_dates as $date_obj) {
        if (!empty($date_obj['date'])) {
            $cleaned_custom_dates[] = [
                'date' => moosebooking_sanitize_input($date_obj['date']),
                'bookable' => !empty($date_obj['bookable']),
                'available' => is_array($date_obj['available']) ? $date_obj['available'] : [],
                'note' => isset($date_obj['note']) ? moosebooking_sanitize_input($date_obj['note'], 'textarea') : ''
            ];
        }
    }

    $data = [
        'name' => $name,
        'description' => $description,
        'active' => $active,
        'updated_at' => current_time('mysql'),
        'time_slots' => wp_json_encode($time_slots),
        'pricing' => wp_json_encode($pricing),
        'custom_dates' => wp_json_encode($cleaned_custom_dates),
    ];

    if ($template_id) {
        // UPPDATERA
        $wpdb->update(
            $table,
            $data,
            ['id' => $template_id]
        );
    } else {
        // NYTT
        $data['created_at'] = current_time('mysql');
        $wpdb->insert(
            $table,
            $data
        );
    }

    // Skicka tillbaka till översikten
    wp_redirect(admin_url('admin.php?page=moosebooking-templates'));
    exit;
}

/**
 * Radera mall (om du senare lägger till delete)
 */
function moosebooking_handle_delete_template() {
    if (!isset($_GET['template_id']) || !current_user_can('manage_options')) {
        wp_die(__('Unauthorized', 'moose-booking'));
    }

    $template_id = intval($_GET['template_id']);

    global $wpdb;
    $table = $wpdb->prefix . 'moosebooking_templates';

    $wpdb->delete(
        $table,
        ['id' => $template_id]
    );

    wp_redirect(admin_url('admin.php?page=moosebooking-templates'));
    exit;
}
