<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.intellipush.com
 * @since      1.0.0
 *
 * @package    Intellipush
 * @subpackage Intellipush/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Intellipush
 * @subpackage Intellipush/public
 * @author     Intellipush <info@intellipush.com>
 */
class Intellipush_Public {
	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Cart parameters
	 *
	 * @since    1.0.0
	 */
	public function cart_parameters() {
		if (IntellipushHelper_isWooCommerceActivated()) {
			global $woocommerce;

			if (isset($_GET['ip_empty_cart'])) {
				$woocommerce->cart->empty_cart();
			}

			if (isset($_GET['ip_merge_cart'])) {
				$items = json_decode(base64_decode($_GET['ip_merge_cart']), true);
				foreach($items as $key => $value) {
					$woocommerce->cart->add_to_cart($value[0], $value[1], $value[2], $value[3]);
				}
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/intellipush-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/intellipush-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'intellipush_public_config', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'merge_cart_code' => IntellipushHelper_getMergeCartCode()
		));
		wp_enqueue_script( $this->plugin_name . '-cart-abandonment', plugin_dir_url( __FILE__ ) . 'js/intellipush-cartAbandonment.js', array( 'jquery' ), $this->version, true );
	}

}
