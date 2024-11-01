<?php

/**
 * Fired during plugin activation.
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
 * WPVE_Activator class.
 *
 * @since    1.0.0
 */
class WPVE_Activator {

	/**
	 * Called when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		$defaults = wpve_get_default_settings();
		
		// Insert the plugin general settings
		if ( false == get_option( 'wpve_general_settings' ) ) {
			add_option( 'wpve_general_settings', $defaults['general'] );
		}
		
		// Insert the plugin brand settings
		if ( false == get_option( 'wpve_brand_settings' ) ) {
			add_option( 'wpve_brand_settings', $defaults['brand'] );
		}
		
		// Insert the plugin privacy settings
		if ( false == get_option( 'wpve_privacy_settings' ) ) {
			add_option( 'wpve_privacy_settings', $defaults['privacy'] );
		}
		
		// Insert the plugin version
		add_option( 'wpve_version', WPVE_PLUGIN_VERSION );

	}

}
