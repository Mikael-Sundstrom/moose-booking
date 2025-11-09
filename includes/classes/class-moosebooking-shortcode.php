<?php
/**
 * Moose Booking Shortcode: [moosebooking]
 *
 * @file includes/shortcodes/class-moosebooking-shortcode.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;

class MooseBooking_Shortcode {

	/**
	 * Initiera shortcoden.
	 */
	public static function init() {
		add_shortcode( 'moosebooking', array( __CLASS__, 'render' ) );
	}

	/**
	 * Renderar frontendens bokningswidget.
	 *
	 * @param array $atts Shortcode-attribut.
	 * @return string HTML-output.
	 */
	public static function render( $atts ) {
		global $wpdb;

		$atts = shortcode_atts(
			array(
				'id'       => 0,
				'template' => '', // name (fallback)
			),
			$atts,
			'moosebooking'
		);

		$table = $wpdb->prefix . 'moosebooking_templates';
		$template = null;

		if ( $atts['id'] ) {
			$template = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $atts['id'] ) );
		} elseif ( $atts['template'] ) {
			$template = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", $atts['template'] ) );
		}

		if ( ! $template ) {
			return '<p>' . esc_html__( 'No booking template found.', 'moose-booking' ) . '</p>';
		}

		ob_start();
		?>
		<div class="moosebooking-widget"
			data-template-id="<?php echo esc_attr( $template->id ); ?>"
			data-template-name="<?php echo esc_attr( $template->name ); ?>">
			<h3><?php echo esc_html( $template->name ); ?></h3>
			<p><?php echo esc_html( $template->description ?? '' ); ?></p>
			<p><?php esc_html_e( 'Booking calendar will appear here.', 'moose-booking' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}


}
