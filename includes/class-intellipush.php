<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.intellipush.com
 * @since      1.0.0
 *
 * @package    Intellipush
 * @subpackage Intellipush/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Intellipush
 * @subpackage Intellipush/includes
 * @author     Intellipush <info@intellipush.com>
 */
class Intellipush {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Intellipush_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'INTELLIPUSH_VERSION' ) ) {
			$this->version = INTELLIPUSH_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'intellipush';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Intellipush_Loader. Orchestrates the hooks of the plugin.
	 * - Intellipush_i18n. Defines internationalization functionality.
	 * - Intellipush_Admin. Defines all hooks for the admin area.
	 * - Intellipush_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-intellipush-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-intellipush-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-intellipush-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-intellipush-public.php';


		/**
		 * Intellipush
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/intellipush-sdk/vendor/autoload.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-intellipush-helper.php';
		foreach (glob(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/tools/*/functions.php') as $file) {
			require_once $file;
		}
		$this->loader = new Intellipush_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Intellipush_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Intellipush_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		error_reporting(E_ERROR);
		ini_set('display_errors','1');

		$plugin_admin = new Intellipush_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'admin_body_class', $plugin_admin, 'add_body_classes' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'add_notices' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'redirect_to_setup' );
		$this->loader->add_action( 'admin_footer_text', $plugin_admin, 'add_footer_credits' );

		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'dashboard_widgets');
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menus' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_admin_fields' );


		$this->loader->add_filter( 'acf/settings/path', $plugin_admin, 'acf_settings_path' );
		$this->loader->add_filter( 'acf/settings/dir', $plugin_admin, 'acf_settings_dir' );
		if ( !get_option('acf_pro_license') && !class_exists('acf') && get_bloginfo('name') !== 'Intellipush' ) {
			add_filter( 'acf/settings/show_admin', '__return_false' );
		}
		
		include_once( plugin_dir_path(__FILE__) . 'acf/acf.php' );
		$this->loader->add_action( 'acf/include_field_types' , $plugin_admin, 'acf_include_field_types' );
		$this->loader->add_action( 'acf/init' , $plugin_admin, 'register_messages_fields' );
		$this->loader->add_filter( 'acf/update_value', $plugin_admin, 'acf_update_value', 10, 3 );

		$this->loader->add_action( 'admin_menu' , $plugin_admin, 'order_menus', 100 );
		$this->loader->add_action( 'init' , $plugin_admin, 'app_output_buffer');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Intellipush_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action( 'template_redirect', $plugin_public, 'cart_parameters' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Intellipush_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
