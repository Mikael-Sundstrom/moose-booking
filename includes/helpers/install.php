<?php if (!defined('ABSPATH')) exit;

function moosebooking_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'moosebooking_templates';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,         /* UNIQUE IDENTIFIER */
        name VARCHAR(255) NOT NULL,                         /* TEMPLATE NAME */
        active TINYINT(1) DEFAULT 0,                        /* ACTIVE OR INACTIVE */
        description TEXT,                                   /* DESCRIPTION */
        time_slots LONGTEXT,                                /* JSON ENCODED TIME SLOTS */
        custom_dates LONGTEXT,                              /* JSON ENCODED MODIFIED DATES (PER DAY) */
        pricing LONGTEXT,                                   /* JSON ENCODED PRICING */
        settings LONGTEXT,                                  /* JSON ENCODED SETTINGS */
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}