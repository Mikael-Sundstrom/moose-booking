<?php if (!defined('ABSPATH')) exit;
/**
 * Plugin Name: Moose Booking
 * Plugin URI: https://dinsida.se/moose-booking
 * Description: Ett enkelt bokningssystem för WordPress.
 * Version: 1.0.0
 * Author: Mikael Sundström
 * Author URI: https://github.com/Mikael-Sundstrom
 * Text Domain: moose-booking
 * Domain Path: /languages
 *
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.8
 *
 * @package moose-booking
 * @version 1.0
 */

// Define global constants
if (!defined('MOOSEBOOKING_PLUGIN_PATH')) define('MOOSEBOOKING_PLUGIN_PATH', plugin_dir_path(__FILE__));
if (!defined('MOOSEBOOKING_PLUGIN_URL')) define('MOOSEBOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load language files
add_action('plugins_loaded', 'moosebooking_load_textdomain');
function moosebooking_load_textdomain() {
    load_plugin_textdomain('moose-booking', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

// A list of required files to load globally
$required_files = [
    'includes/helpers/init.php',    // Load helper functions
    'includes/handlers/init.php',   // Load form handlers
];

if (is_admin()) {
    $required_files[] = 'includes/menu.php'; // Load admin resources
    $required_files[] = 'includes/hooks-admin.php'; // Load admin hooks
    $required_files[] = 'includes/calendar.php'; // Load calendar functions
}
else {
    $required_files[] = 'includes/hooks-frontend.php'; // Load frontend resources
}

// Load all required files
foreach ($required_files as $file) {
	$file_path = MOOSEBOOKING_PLUGIN_PATH . $file;
	if (file_exists($file_path)) {
		require_once $file_path;
	}
}

// Acitivation hook to create database tables
register_activation_hook(__FILE__, 'moosebooking_install');