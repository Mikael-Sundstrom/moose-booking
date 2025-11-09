<?php
/**
 * Register Gutenberg blocks for Moose Booking
 *
 * @file includes/blocks/register-blocks.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;
error_log('✅ MooseBooking: register-blocks.php loaded');

add_action( 'init', function() {
	wp_register_script(
		'moosebooking-block-booking',
		MOOSEBOOKING_URL . 'assets/blocks/booking/index.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch' ),
		filemtime( MOOSEBOOKING_PATH . 'assets/blocks/booking/index.js' )
	);

	register_block_type( 'moose-booking/booking', array(
		'editor_script' => 'moosebooking-block-booking',
		'render_callback' => function( $attributes ) {
			$template = $attributes['template'] ?? '';
			$id       = $attributes['id'] ?? 0;

			// Anropa din shortcode internt (så att logiken delas)
			return do_shortcode( sprintf(
				'[moosebooking %s]',
				$id ? 'id="' . intval( $id ) . '"' : 'template="' . esc_attr( $template ) . '"'
			));
		},
	) );
});
