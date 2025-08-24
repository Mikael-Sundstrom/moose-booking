<?php if (!defined('ABSPATH')) exit;

/**
 * Includes/form-handlers/init.php
 * Loads all form-handlers for the plugin.
 */

// Lista över form-handlers
$handlers = [
    'template-editor-handler.php',
];

foreach ($handlers as $handler) {
    $file_path = MOOSEBOOKING_PLUGIN_PATH . 'includes/handlers/' . $handler;

    moosebooking_safe_include($file_path);
}
