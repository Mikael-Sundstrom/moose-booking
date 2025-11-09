<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MooseBooking_CountryCodes {
	/**
	 * Returnerar en associerad lista med landskoder.
	 *
	 * @return array
	 */
	public static function get_all() {
		return array(
			'+1'   => '🇺🇸 United States / Canada',
			'+44'  => '🇬🇧 United Kingdom',
			'+45'  => '🇩🇰 Denmark',
			'+46'  => '🇸🇪 Sweden',
			'+47'  => '🇳🇴 Norway',
			'+49'  => '🇩🇪 Germany',
			'+33'  => '🇫🇷 France',
			'+34'  => '🇪🇸 Spain',
			'+39'  => '🇮🇹 Italy',
			'+358' => '🇫🇮 Finland',
			'+61'  => '🇦🇺 Australia',
			'+81'  => '🇯🇵 Japan',
			'+91'  => '🇮🇳 India',
			'+86'  => '🇨🇳 China',
			// Lägg gärna till fler om du vill ha en komplett lista
		);
	}
}
