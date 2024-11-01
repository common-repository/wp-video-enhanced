<?php

/**
 * The file that defines the core plugin class.
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
 * WPVE - The main plugin class.
 *
 * @since    1.0.0
 */
class WPVE {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       WPVE_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once WPVE_PLUGIN_DIR . 'includes/class-wpve-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WPVE_PLUGIN_DIR . 'includes/class-wpve-i18n.php';
		
		/**
		 * The file that holds the general helper functions.
		 */
		require_once WPVE_PLUGIN_DIR . 'includes/functions.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once WPVE_PLUGIN_DIR . 'admin/class-wpve-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WPVE_PLUGIN_DIR . 'public/class-wpve-public.php';		

		$this->loader = new WPVE_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function set_locale() {

		$plugin_i18n = new WPVE_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WPVE_Admin();
		
		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'manage_upgrades' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		
		$this->loader->add_filter( 'plugin_action_links_' . WPVE_PLUGIN_FILE_NAME, $plugin_admin, 'plugin_action_links' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function define_public_hooks() {

		$plugin_public = new WPVE_Public();
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_scripts' );
		$this->loader->add_action( 'wp_ajax_wpve_set_cookie', $plugin_public, 'set_cookie' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpve_set_cookie', $plugin_public, 'set_cookie' );
		
		$this->loader->add_filter( 'mejs_settings', $plugin_public, 'mejs_settings' );
		$this->loader->add_filter( 'wp_video_extensions', $plugin_public, 'video_extensions' );
		$this->loader->add_filter( 'mime_types', $plugin_public, 'mime_types' );
		$this->loader->add_filter( 'shortcode_atts_video', $plugin_public, 'shortcode_atts', 11, 3 );
		$this->loader->add_filter( 'wp_video_shortcode', $plugin_public, 'video_shortcode', 11, 5 );
		$this->loader->add_filter( 'embed_oembed_html', $plugin_public, 'embed_oembed_html', 99, 4 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

}
