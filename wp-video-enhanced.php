<?php

/**
 * The plugin bootstrap file.
 *
 * @link           https://plugins360.com
 * @since          1.0.0
 * @package        WP_Video_Enhanced
 *
 * @wordpress-plugin
 * Plugin Name:    WP Video Enhanced
 * Plugin URI:     https://plugins360.com
 * Description:    Extending WordPress Video Shortcode with New Features. Logo & Branding, GDPR Consent, HLS, M(PEG)-DASH, Live Streaming, Configure Initial Volume and lot more.
 * Version:        1.3.0
 * Author:         Team Plugins360
 * License:        GPL-2.0+
 * License URI:    http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:    wp-video-enhanced
 * Domain Path:    /languages
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

// The current version of the plugin
if ( ! defined( 'WPVE_PLUGIN_VERSION' ) ) {
    define( 'WPVE_PLUGIN_VERSION', '1.3.0' );
}

// The unique identifier of the plugin
if ( ! defined( 'WPVE_PLUGIN_SLUG' ) ) {
    define( 'WPVE_PLUGIN_SLUG', 'wp-video-enhanced' );
}

// Path to the plugin directory
if ( ! defined( 'WPVE_PLUGIN_DIR' ) ) {
    define( 'WPVE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// URL of the plugin
if ( ! defined( 'WPVE_PLUGIN_URL' ) ) {
    define( 'WPVE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// The plugin file name
if ( ! defined( 'WPVE_PLUGIN_FILE_NAME' ) ) {
    define( 'WPVE_PLUGIN_FILE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpve-activator.php
 */
function activate_wpve() {

	require_once WPVE_PLUGIN_DIR . 'includes/class-wpve-activator.php';
	WPVE_Activator::activate();
	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpve-deactivator.php
 */
function deactivate_wpve() {

	require_once WPVE_PLUGIN_DIR . 'includes/class-wpve-deactivator.php';
	WPVE_Deactivator::deactivate();
	
}

register_activation_hook( __FILE__, 'activate_wpve' );
register_deactivation_hook( __FILE__, 'deactivate_wpve' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WPVE_PLUGIN_DIR . 'includes/class-wpve.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wpve() {

	$plugin = new WPVE();
	$plugin->run();
	
}

run_wpve();
