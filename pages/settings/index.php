<?php
/**
 * Admin page for managing settings.
 *
 * @file pages/settings/index.php
 * @package moose-booking
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<hr class="wp-header-end">

	<?php
	// Determine the active tab.
	$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';

	// Register available tabs. Can be filtered by other modules later.
	$tabs = array(
		'general'       => __( 'General', 'moose-booking' ),
		'notifications' => __( 'Notifications', 'moose-booking' ),
		'appearance'    => __( 'Appearance', 'moose-booking' ),
		'limits'        => __( 'Limits', 'moose-booking' ),
		'about'         => __( 'About', 'moose-booking' ),
	);

	/**
	 * Allow developers or extensions to add more settings tabs.
	 *
	 * @param array $tabs Array of tab slugs and labels.
	 */
	$tabs = apply_filters( 'moosebooking_settings_tabs', $tabs );
	?>

	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $slug => $label ) :
			$active_class = ( $active_tab === $slug ) ? ' nav-tab-active' : '';
			printf(
				'<a href="%1$s" class="nav-tab%3$s">%2$s</a>',
				esc_url( admin_url( 'admin.php?page=moosebooking-settings&tab=' . $slug ) ),
				esc_html( $label ),
				esc_attr( $active_class )
			);
		endforeach;
		?>
	</h2>

	<div class="moosebooking-tab-content">
		<?php
		// Load the correct tab content.
		switch ( $active_tab ) {
			case 'general':
				// You can easily add more files to the general tab here.
				$general_sections = array( 'general.php', 'email.php' );
				foreach ( $general_sections as $section_file ) {
					$file_path = plugin_dir_path( __FILE__ ) . 'tabs/' . $section_file;
					if ( file_exists( $file_path ) ) {
						require_once $file_path;
					}
				}
				break;

			case 'notifications':
				require_once plugin_dir_path( __FILE__ ) . 'tabs/notifications.php';
				break;

			case 'appearance':
				require_once plugin_dir_path( __FILE__ ) . 'tabs/appearance.php';
				break;

			case 'limits':
				require_once plugin_dir_path( __FILE__ ) . 'tabs/limits.php';
				break;

			case 'about':
				require_once plugin_dir_path( __FILE__ ) . 'tabs/about.php';
				break;

			default:
				echo '<p>' . esc_html__( 'Invalid tab selected.', 'moose-booking' ) . '</p>';
				break;
		}
		?>
	</div>
</div>
