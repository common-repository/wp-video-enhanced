<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://plugins360.com
 * @since      1.0.0
 *
 * @package    WP_Video_Enhanced
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wpve_general_settings' );
delete_option( 'wpve_brand_settings' );
delete_option( 'wpve_privacy_settings' );
delete_option( 'wpve_version' );