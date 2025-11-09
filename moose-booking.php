<?php
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
 * Requires at least: 6.1
 * Tested up to: 6.8
 *
 * @package moose-booking
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define plugin constants early.
 * These are used across the plugin to avoid repeated path/url calculations.
 */
if ( ! defined( 'MOOSEBOOKING_FILE' ) ) {
	define( 'MOOSEBOOKING_FILE', __FILE__ );
}
if ( ! defined( 'MOOSEBOOKING_PATH' ) ) {
	define( 'MOOSEBOOKING_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'MOOSEBOOKING_URL' ) ) {
	define( 'MOOSEBOOKING_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'MOOSEBOOKING_BASENAME' ) ) {
	define( 'MOOSEBOOKING_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'MOOSEBOOKING_VERSION' ) ) {
	define( 'MOOSEBOOKING_VERSION', '1.0.0' );
}

/**
 * Bootstrap after all plugins are loaded.
 */
add_action(
	'plugins_loaded',
	static function () {

		// Include shared helpers/handlers.
		require_once MOOSEBOOKING_PATH . 'includes/helpers/init.php';
		require_once MOOSEBOOKING_PATH . 'includes/handlers/init.php';
		require_once MOOSEBOOKING_PATH . 'includes/classes/class-moosebooking-rest.php';

		// Branch for admin vs frontend using hooks (safer for AJAX/cron).
		if ( is_admin() ) {
			add_action(
				'init',
				static function () {
					require_once MOOSEBOOKING_PATH . 'includes/hooks-admin.php';
					require_once MOOSEBOOKING_PATH . 'includes/menu.php';
					require_once MOOSEBOOKING_PATH . 'includes/calendar.php';
				}
			);
		} else {
			add_action(
				'init',
				static function () {
					require_once MOOSEBOOKING_PATH . 'includes/hooks-frontend.php';

					// Load shortcode class
					require_once MOOSEBOOKING_PATH . 'includes/classes/class-moosebooking-shortcode.php';
					MooseBooking_Shortcode::init();
				}
			);
		}
	}
);
error_log('✅ MooseBooking: register-blocks.php loaded');

// 🧱 Block-registrering separat (gäller även blockeditor i admin)
add_action( 'init', function() {
	require_once MOOSEBOOKING_PATH . 'includes/blocks/register-blocks.php';
});

/**
 * Activation callback.
 * Includes installer only when needed to keep bootstrap light.
 */
function moosebooking_activate() {
	$path = MOOSEBOOKING_PATH . 'includes/helpers/install.php';
	require_once $path;
	if ( function_exists( 'moosebooking_install' ) ) {
		moosebooking_install();
	}
}
register_activation_hook( __FILE__, 'moosebooking_activate' );

/**
 * Deactivation callback (optional).
 */
function moosebooking_deactivate_cb() {
	$path = MOOSEBOOKING_PATH . 'includes/helpers/install.php';
	if ( file_exists( $path ) ) {
		require_once $path;
		if ( function_exists( 'moosebooking_deactivate' ) ) {
			moosebooking_deactivate();
		}
	}
}
register_deactivation_hook( __FILE__, 'moosebooking_deactivate_cb' );

/**
 * Uninstall callback.
 * Prefer uninstall.php file guarded with ABSPATH and WP_UNINSTALL_PLUGIN.
 */
function moosebooking_uninstall_cb() {
	$path = MOOSEBOOKING_PATH . 'uninstall.php';
	if ( file_exists( $path ) ) {
		require $path;
	}
}
register_uninstall_hook( __FILE__, 'moosebooking_uninstall_cb' );
