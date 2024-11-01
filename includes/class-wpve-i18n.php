<?php

/**
 * Define the internationalization functionality.
 *
 * @link          https://plugins360.com
 * @since         1.0.0
 *
 * @package       WP_Video_Enhanced
 * @subpackage    WP_Video_Enhanced/includes
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WPVE_i18n class.
 *
 * @since    1.0.0
 */
class WPVE_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-video-enhanced',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
