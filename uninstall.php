<?php if (!defined('WP_UNINSTALL_PLUGIN')) exit;

global $wpdb;

// Din tabell
$table_name = $wpdb->prefix . 'moosebooking_templates';

// Radera tabellen
$wpdb->query("DROP TABLE IF EXISTS $table_name");
