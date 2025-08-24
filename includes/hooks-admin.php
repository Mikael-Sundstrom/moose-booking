<?php if (!defined('ABSPATH')) exit;

/**
 * includes/admin-hooks.php
 * Handles hooks and logic specific to the WordPress admin panel.
 */

add_action('admin_init', function () {

    // Register settings for "Email"
    register_setting('moosebooking_email_settings_group', 'moosebooking_email_to_admin');
    register_setting('moosebooking_email_settings_group', 'moosebooking_email_message_subject');
    register_setting('moosebooking_email_settings_group', 'moosebooking_email_user_message');
    register_setting('moosebooking_email_settings_group', 'moosebooking_email_notify_admin');

    // Register settings for "Appearance"
    register_setting('moosebooking_appearance_settings_group', 'moosebooking_appearance_primary_color');
    register_setting('moosebooking_appearance_settings_group', 'moosebooking_appearance_calendar_view');
    register_setting('moosebooking_appearance_settings_group', 'moosebooking_appearance_show_week_numbers');

    // Register settings for "Limits"
    register_setting('moosebooking_limits_settings_group', 'moosebooking_limits_max_bookings_per_ip');
    register_setting('moosebooking_limits_settings_group', 'moosebooking_limits_min_hours_before_booking');
    register_setting('moosebooking_limits_settings_group', 'moosebooking_limits_max_days_ahead');

    // Register settings for "Notifications"
    register_setting('moosebooking_twilio_settings_group', 'moosebooking_twilio_account_sid');
    register_setting('moosebooking_twilio_settings_group', 'moosebooking_twilio_auth_token');
    register_setting('moosebooking_twilio_settings_group', 'moosebooking_twilio_to_phone_number');
    register_setting('moosebooking_twilio_settings_group', 'moosebooking_twilio_from_phone_number');
    register_setting('moosebooking_twilio_settings_group', 'moosebooking_twilio_sms_notify_admin');
});

add_action('admin_enqueue_scripts', function () {

    $screen = get_current_screen();
    if (!$screen) return;

    // Alla sidor i pluginet (med rätt sluggar)
    $allowed_screens = [
        'toplevel_page_moosebooking',
        'booking_page_moosebooking-templates',
        'booking_page_moosebooking-template-editor',
        'booking_page_moosebooking-bookings',
        'booking_page_moosebooking-settings'
    ];
    
    // Gemensam admin-JS för pluginet
    wp_enqueue_script(
        'admin-moosebooking-js',
        MOOSEBOOKING_PLUGIN_URL . 'assets/javascripts/admin-moosebooking.js',
        [],
        filemtime(MOOSEBOOKING_PLUGIN_PATH . 'assets/javascripts/admin-moosebooking.js'),
        true
    );
    
    if (!in_array($screen->id, $allowed_screens)) return;

    /****************************************************************
     * Gemensam CSS och JS för pluginet
     * Denna laddas alltid när någon av pluginets sidor är aktiv
     **************************************************************/
    wp_enqueue_style(
        'admin-moosebooking-css',
        MOOSEBOOKING_PLUGIN_URL . 'assets/stylesheets/admin-moosebooking.css',
        [],
        filemtime(MOOSEBOOKING_PLUGIN_PATH . 'assets/stylesheets/admin-moosebooking.css')
    );
    
    /***************************************************************
     * Specifik CSS och JS för template-editorn
     * Denna laddas endast när användaren är på template-editorn
     **************************************************************/
    if ($screen->id === 'booking_page_moosebooking-template-editor') {

        // Specifik CSS för template-editorn
        wp_enqueue_style(
            'admin-template-editor-css',
            MOOSEBOOKING_PLUGIN_URL . 'assets/stylesheets/admin-template-editor.css',
            [],
            filemtime(MOOSEBOOKING_PLUGIN_PATH . 'assets/stylesheets/admin-template-editor.css')
        );
        
        // Specifik JS för template-editorn
        wp_enqueue_script(
            'admin-template-editor-js',
            MOOSEBOOKING_PLUGIN_URL . 'assets/javascripts/admin-template-editor.js',
            [],
            filemtime(MOOSEBOOKING_PLUGIN_PATH . 'assets/javascripts/admin-template-editor.js'),
            true
        );

        wp_localize_script('admin-template-editor-js', 'moosebooking_strings', [
            'type' => __('Type', 'moose-booking'),
            'price' => __('Price', 'moose-booking'),
            'comment' => __('Comment', 'moose-booking'),
            'remove' => __('×', 'moose-booking'),
            'hourly' => __('Hourly', 'moose-booking'),
            'daily' => __('Daily', 'moose-booking'),
            'weekend' => __('Weekend', 'moose-booking'),
            'weekly' => __('Weekly', 'moose-booking'),
            'custom' => __('Custom', 'moose-booking'),
        ]);
        /***************************************************************
         * Ladda in lokaliserade variabler för AJAX-anrop
         * Denna laddas endast när användaren är på template-editorn
         **************************************************************/
        wp_localize_script('admin-template-editor-js', 'moosebooking_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('moosebooking_nonce')
        ]);
    }
});

add_filter('submenu_file', function($submenu_file) {
    if (isset($_GET['page']) && $_GET['page'] === 'moosebooking-template-editor') {
        return 'moosebooking-templates';
    }
    return $submenu_file;
});


add_action('wp_ajax_moosebooking_generate_calendar', function () {

    check_ajax_referer('moosebooking_nonce', 'nonce');

    $year = intval($_POST['year']);
    $month = intval($_POST['month']);

    if (!$year || !$month) {
        wp_send_json_error('Ogiltigt år eller månad.');
        wp_die();
    }

    $template_id = intval($_POST['template_id'] ?? 0);
    $custom_dates = [];

    // 🟢 Om custom_dates skickades med från JavaScript -> använd dem
    if (!empty($_POST['custom_dates'])) {
        $custom_dates = json_decode(stripslashes($_POST['custom_dates']), true) ?: [];
    }
    // 🔵 Annars hämta från databasen
    elseif ($template_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'moosebooking_templates';
        $template = $wpdb->get_row($wpdb->prepare(
            "SELECT custom_dates FROM $table WHERE id = %d", $template_id
        ));
        if ($template && $template->custom_dates) {
            $custom_dates = json_decode($template->custom_dates, true) ?: [];
        }
    }

    // ✅ Använd render-funktionen
    $html = moosebooking_render_calendar($year, $month, $custom_dates);
    echo $html;
    wp_die();
});

