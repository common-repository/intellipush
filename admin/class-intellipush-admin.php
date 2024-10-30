<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.intellipush.com
 * @since      1.0.0
 *
 * @package    Intellipush
 * @subpackage Intellipush/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Intellipush
 * @subpackage Intellipush/admin
 * @author     Intellipush <info@intellipush.com>
 */

use Intellipush\Intellipush;

class Intellipush_Admin {
	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * ACF settings path
	 *
	 * @since    1.0.0
	 */
	public function acf_settings_path( $path ) {
		$path = dirname(plugin_dir_path(__FILE__)) . 'includes/acf/';
		return $path;
	}

	/**
	 * ACF settings dir
	 *
	 * @since    1.0.0
	 */
	public function acf_settings_dir( $dir ) {
		$dir = dirname(plugin_dir_url(__FILE__)) . '/includes/acf/';
		return $dir;
	}

	/**
	 * Add admin body classes
	 *
	 * @since    1.0.0
	 */
	public function add_body_classes( $classes ) {
		$current_page = isset($_GET['page']) ? $_GET['page'] : false;
		if ( $current_page && strpos($current_page, 'intellipush') !== false ) {
			$classes .= ' intellipush';
		}
		if (!IntellipushHelper_isApiSetup()) {
			$classes .= ' intellipush-no-setup';
		}
		if (!IntellipushHelper_isAuth()) {
			$classes .= ' intellipush-no-auth';
		}
        return $classes;
	}

	/**
	 * Add admin notices
	 *
	 * @since    1.0.0
	 */
	public function add_notices() {
		$excludePages = !in_array(get_current_screen()->id, array('intellipush_page_intellipush-settings', 'intellipush_page_intellipush-setup'));
		if(!IntellipushHelper_isAuth()) {
			if (IntellipushHelper_isApiSetup()) {
				echo '<div class="notice notice-error">';
				echo 	'<p>';
				echo 		'<strong>Intellipush:</strong> ';
				echo 		IntellipushHelper_getUserInfo()->response . ', ';
				echo 		__('please enter your <strong>API information</strong>', 'intellipush');
				if ($excludePages) {
					echo ' <a href="'. menu_page_url('intellipush-settings', false) . '">' . __('here', 'intellipush') . '</a>';
				}
				echo 	'</p>';
				echo '</div>';
			}
			if (!IntellipushHelper_isApiSetup() && $excludePages) {
				echo '<div class="notice notice-info">';
				echo 	'<h3 class="ip--margin-bottom-0">Intellipush:</h3>';
				echo 	'<p>';
				echo 		__('Welcome to Intellipush SMS for Wordpress, please finish your setup.', 'intellipush');
				echo 	'</p>';
				echo 	'<p>';
				if ( !in_array(get_current_screen()->id, array('intellipush_page_intellipush-settings', 'intellipush_page_intellipush-setup')) ) {
					echo '<a href="'. menu_page_url('intellipush-setup', false) . '" class="button button-primary">' . __('Setup Now', 'intellipush') . '</a>';
				}
				echo 	'</p>';
				echo '</div>';
			}
		}
	}

	/**
	 * Add admin redirection when has no API ID & Secret key
	 *
	 * @since    1.0.0
	 */
	public function redirect_to_setup() {
		$current_page = isset($_GET['page']) ? $_GET['page'] : false;
		if (
			$current_page && strpos($current_page, 'intellipush') !== false &&
			$current_page !=='intellipush-settings' &&
			$current_page !=='intellipush-setup' &&
			!IntellipushHelper_isApiSetup()
		) {
			wp_redirect( menu_page_url('intellipush-setup', false), 301 );
        	die;
		}
	}

	/**
	 * Add admin footer credits
	 *
	 * @since    1.0.0
	 */
	public function add_footer_credits($footer_text) {
		$intellipush_footer_text = '';
		$current_page = isset($_GET['page']) ? $_GET['page'] : false;
		if ( $current_page && strpos($current_page, 'intellipush') !== false ) {
			$intellipush_footer_text = '<div class="ip--font-style-italic">This plugin created & copyright © '.date('Y').' by <strong>Intellipush AS</strong> – <a href="https://intellipush.com" target="_blank">Intellipush</a> – <a href="tel:+4741329999">+47 413 29 999</a> – <a href="mailto:info@intellipush.com">info@intellipush.com</a> – <a href="'.menu_page_url('intellipush-_credits', false).'">Thanks & credits</a></div>';
		}
		return $intellipush_footer_text . $footer_text;
	}

	/**
	 * Add admin menus / submenus
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menus() {
		add_menu_page(
			'Intellipush',
			'Intellipush',
			'manage_options',
			'intellipush',
			array($this, 'admin_page_main'),
			'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path fill="transparent" d="M9.2 7.2c.2-.1.3-.1.5-.1.5 0 1 .2 1.3.6.2.3.3.8.3 1.6v3.2c0 .8-.1 1.3-.2 1.5-.2.5-.6.9-1.4.9-.6 0-1.1-.3-1.4-.8v3.8H6.7V9.2l1.6-1.3c.3-.3.6-.5.9-.7m-1 5.4c0 .4.3.8.7.8.6 0 .7-.5.7-.8v-3c0-.5-.2-1-.7-1-.4 0-.7.4-.7 1v3zM8.2 1.7H6.7V.1h1.5v1.6zm0 1.5v3.6L6.7 8.1V3.2h1.5z"/></svg>'),
			!IntellipushHelper_isApiSetup() ? 2 : null
		);

		acf_add_options_sub_page( array('page_title' => __('Messages','intellipush'), 'menu_title'	=> __('Messages','intellipush'), 'menu_slug' => 'intellipush-messages', 'parent_slug'	=> 'intellipush') );
		acf_add_options_sub_page( array('page_title' => __('Tools','intellipush'), 'menu_title'	=> __('Tools','intellipush'), 'menu_slug' => 'intellipush-tools', 'parent_slug'	=> 'intellipush') );
		
		add_submenu_page('intellipush', __('Settings', 'intellipush'), __('Settings','intellipush'), 'manage_options', 'intellipush-settings', array($this, 'admin_page_settings'));
		add_submenu_page('intellipush', __('Setup', 'intellipush'), __('Setup','intellipush'), 'manage_options', 'intellipush-setup', array($this, 'admin_page_setup'));
		add_submenu_page('intellipush', __('Welcome', 'intellipush'), __('Welcome','intellipush'), 'manage_options', 'intellipush-welcome', array($this, 'admin_page_welcome'));
		add_submenu_page('intellipush', __('Credits', 'intellipush'), __('_Credits','intellipush'), 'manage_options', 'intellipush-_credits', array($this, 'admin_page__credits'));
	}

	/**
	 * Add Dashboard widgets
	 *
	 * @since    1.0.0
	 */
	public function dashboard_widgets() {
		if (IntellipushHelper_isApiSetup() && IntellipushHelper_isAuth()) {
			acf_form_head();
			wp_add_dashboard_widget( 'intellipush_dashboard_widget_statistics', 'Intellipush: Statistics', array( $this, 'add_dashboard_widget_statistics' ) );
			wp_add_dashboard_widget( 'intellipush_dashboard_widget_sendNow', 'Intellipush: Send now', array( $this, 'add_dashboard_widget_sendNow' ) );
		}
	}

	public function add_dashboard_widget_statistics() {
		if (IntellipushHelper_isAuth()) {
			$Intellipush_isUserInfo = IntellipushHelper_getUserInfo();
			$Intellipush_isAuth = IntellipushHelper_isAuth();

			$Intellipush_Statistics = IntellipushHelper_getStatistics();
			$Intellipush_Statistics = $Intellipush_Statistics->success ? $Intellipush_Statistics->response->data->numberOf : null;
			echo '<div class="inside-content">';
			echo 	'<div class="ip--float-left"><svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 18 18"><path fill="#0085ba" d="M9.2 7.2c.2-.1.3-.1.5-.1.5 0 1 .2 1.3.6.2.3.3.8.3 1.6v3.2c0 .8-.1 1.3-.2 1.5-.2.5-.6.9-1.4.9-.6 0-1.1-.3-1.4-.8v3.8H6.7V9.2l1.6-1.3c.3-.3.6-.5.9-.7m-1 5.4c0 .4.3.8.7.8.6 0 .7-.5.7-.8v-3c0-.5-.2-1-.7-1-.4 0-.7.4-.7 1v3zM8.2 1.7H6.7V.1h1.5v1.6zm0 1.5v3.6L6.7 8.1V3.2h1.5z"/></svg></div>';
			echo 	'<ul class="ip--margin-0">';
			echo		'<li>';
			echo			'<span class="dashicons dashicons-arrow-right"></span>';
			echo			sprintf(_n('<strong>%s</strong> contact', '<strong>%s</strong> contacts', $Intellipush_Statistics->contacts, 'intellipush'), $Intellipush_Statistics->contacts);
			echo		'</li>';
			echo		'<li>';
			echo			'<span class="dashicons dashicons-arrow-right"></span>';
			echo			sprintf(_n('<strong>%s</strong> contactlist', '<strong>%s</strong> contactlists', $Intellipush_Statistics->contactlists, 'intellipush'), $Intellipush_Statistics->contactlists);
			echo		'</li>';
			echo		'<li>';
			echo		'<span class="dashicons dashicons-arrow-right"></span>';
			echo			sprintf(_n('<strong>%s</strong> scheduled messages', '<strong>%s</strong> scheduled messages', $Intellipush_Statistics->unsendtNotifications, 'intellipush'), $Intellipush_Statistics->unsendtNotifications);
			echo		'</li>';
			echo 	'</ul>';
			echo '</div>';

			echo '<div class="ip--dashboard-widget-footer ip--text-align-right"><a href="'. menu_page_url('intellipush', false) .'" class="button button-primary">'. __('View more statistics','intellipush') .'</a></div>';
		} else {
			echo '<div class="inside-content">';
			echo 	'<p>'. __('Welcome to Intellipush plugin, seems like you are not authenticated yet.','intellipush') .'</p>';
			echo '</div>';
			echo '<div class="ip--dashboard-widget-footer ip--text-align-right"><a href="'. menu_page_url('intellipush-settings', false) .'" class="button button-primary">'. __('Add your Intellipush API information?','intellipush') .'</a></div>';
		}
	}
	public function add_dashboard_widget_sendNow() {
		acf_form(array(
			'id'					=> 'acf-group_intellipush_messages_sendNow',
			'field_groups'			=> array('group_intellipush_messages_sendNow'),
			'form_attributes'		=> array('class' => ''),
			'html_before_fields'	=> '<div class="intellipush">',
			'html_after_fields'		=> '</div>',
			'html_submit_button'	=> false,
		));
		
	}


	public function order_menus( $menu_ord ) {
		global $submenu;
		$orderBy = array(
			__('Intellipush','intellipush'),
			__('Messages','intellipush'),
			__('Tools','intellipush'),
			__('Settings','intellipush'),
			__('Setup','intellipush'),
			__('Welcome','intellipush'),
			__('_Credits','intellipush')
		);

		$tmp = array();
		foreach ($orderBy as $order) {
			foreach ($submenu['intellipush'] as $k => $v) {
				if ($order === $v[0]) {
					$tmp[] = $v;
				}
			}
		}
		$submenu['intellipush'] = $tmp;
	}

	public function admin_page_main() {
		include_once 'pages/_header/index.php';
		include_once 'pages/main/index.php';
	}

	public function admin_page_settings() {
		include_once 'pages/_header/index.php';
		include_once 'pages/settings/index.php';
	}

	public function admin_page_statistics() {
		include_once 'pages/_header/index.php';
		include_once 'pages/statistics/index.php';
	}

	public function admin_page_setup() {
		include_once 'pages/_header/index.php';
		include_once 'pages/setup/index.php';
	}

	public function admin_page_welcome() {
		include_once 'pages/_header/index.php';
		include_once 'pages/welcome/index.php';
	}

	public function admin_page__credits() {
		include_once 'pages/_header/index.php';
		include_once 'pages/_credits/index.php';
	}

	/**
	 * Register admin fields
	 *
	 * @since    1.0.0
	 */
	public function register_admin_fields() {

		// Settings page
		add_settings_section(
			$this->plugin_name . '_settings',
			__('API information', 'intellipush'),
			false,
			$this->plugin_name . '_settings'
		);
		add_settings_field(
			$this->plugin_name . '_settings_api_id',
			__('Api ID', 'intellipush'),
			array($this, $this->plugin_name . '_settings_api_id_cb'),
			$this->plugin_name . '_settings',
			$this->plugin_name . '_settings',
			array('label_for' => $this->plugin_name . '_settings_api_id')
		);
		register_setting($this->plugin_name . '_settings', $this->plugin_name . '_settings_api_id');

		add_settings_field(
			$this->plugin_name . '_settings_api_secret_key',
			__('Secret key', 'intellipush'),
			array($this, $this->plugin_name . '_settings_api_secret_key_cb'),
			$this->plugin_name . '_settings',
			$this->plugin_name . '_settings',
			array('label_for' => $this->plugin_name . '_settings_api_secret_key')
		);
		register_setting($this->plugin_name . '_settings', $this->plugin_name . '_settings_api_secret_key');

		// Tools : WooCommerce events
		register_setting($this->plugin_name . '_tools_woocommerceEvents', $this->plugin_name . '_tools_woocommerceEvents_events');

	}

	/**
	 * Admin field callbacks
	 *
	 * @since    1.0.0
	 */
	public function intellipush_settings_api_id_cb() {
		$name = $this->plugin_name . '_settings_api_id';
		$value = get_option($name);
		echo '<input type="text" class="regular-text" name="'.$name.'" id="'.$name.'" value="'.$value.'" pattern="^[0-9]{7}" autocomplete="off">';
	}
	public function intellipush_settings_api_secret_key_cb() {
		$name = $this->plugin_name . '_settings_api_secret_key';
		$value = get_option($name);
		echo '<input type="password" class="regular-text" name="'.$name.'" id="'.$name.'" value="'.$value.'" pattern="^[a-z0-9]{32}" autocomplete="new-password">';
		echo '<p class="description" id="tagline-description"><a href="'.IntellipushHelper_getConnectUrl().'" class="button ip--font-style-normal">Intellipush Connect</a> <small>to get Api ID & secret key or find it <a href="' . IntellipushHelper_getHomeUrl() . '/settings/api" target="_blank">here</a> or <a href="' . IntellipushHelper_getHomeUrl() . '/developer/subaccounts" target="_blank">subaccount</a></small></p>';
	}

	/**
	 * Register Messages fields
	 *
	 * @since    1.0.0
	 */
	public function register_messages_fields()  {
		$sendNowTargets = IntellipushHelper_getContactlist();
		if ($sendNowTargets) {
			foreach ($sendNowTargets as $key => $value) {
				$sendNowTargets[$value->id] =$sendNowTargets[$key]->list_name;
				unset($sendNowTargets[$key]);
			}
		}

		acf_add_local_field_group(array(
			'key' => 'group_intellipush_messages_sendNow',
			'title' => __('Send now', 'intellipush'),
			'fields' => array(
				array(
					'key' => 'field_intellipush_messages_sendNow',
					'name' => 'intellipush_messages_sendNow',
					'label' => __('Message', 'intellipush') . ' <a href="#" class="ip--send-now-load-message-templates ip--font-weight-normal"><small>('.__('templates', 'intellipush').')</small></a>',
					'type' => 'textarea',
					'instructions' => '<small class="ip--sms-counter ip--font-style-normal">About <span class="ip--sms-counter-length">0</span>/159 characters left <small>(<span class="ip--sms-counter-number-of-sms">1</span> SMS per receiver)</small></small>',
					'rows' => 3,
					'wrapper' => array(
						'class' => 'ip--sms-counter-field'
					)
				),
				array(
					'key' => 'field_intellipush_messages_sendNow_target',
					'label' => __('Target', 'intellipush') . ' <small class="acf-accordion-view ip--font-weight-normal ip--color-link">(' . __('view', 'intellipush') . ')</small>',
					'name' => '',
					'type' => 'accordion',
					'open' => 0,
					'multi_expand' => 0,
					'endpoint' => 0,
					'required' => 1
				),
				array(
					'key' => 'field_intellipush_messages_sendNow_delay',
					'label' => __('Delay', 'intellipush'),
					'name' => 'delay',
					'type' => 'number',
					'wrapper' => array(
						'width' => '50'
					),
					'append' => __('minutes', 'intellipush'),
					'min' => '0'
				),
				array(
					'key' => 'field_intellipush_messages_sendNow_repeat',
					'label' => __('Repeat', 'intellipush'),
					'name' => 'repeat',
					'type' => 'select',
					'wrapper' => array(
						'width' => '50'
					),
					'choices' => array(
						'never' 	=> __('Never', 'intellipush'),
						'daily' 	=> __('Daily', 'intellipush'),
						'weekly' 	=> __('Weekly', 'intellipush'),
						'monthly' 	=> __('Monthly', 'intellipush')
					),
					'ui' => 1,
					'return_format' => 'value',
				),
				array(
					'key' => 'field_intellipush_messages_sendNow_contactlist',
					'name' => 'intellipush_messages_sendNow_contactlist',
					'label' => __('Contactlist', 'intellipush'),
					'type' => 'select',
					'wrapper' => array(
						'width' => '50'
					),
					'choices' => $sendNowTargets,
					'ui' => 1,
					'allow_null' => 1,
				),
				array(
					'key' => 'field_intellipush_messages_sendNow_telephone',
					'name' => 'intellipush_messages_sendNow_telephon',
					'label' => __('Telephone', 'intellipush'),
					'type' => 'text',
					'wrapper' => array(
						'width' => '50'
					),
					'placeholder' => IntellipushHelper_getRecommendedPhoneCode() . 'XXXXXXXX'
				),
				array(
					'key' => 'field_intellipush_messages_sendNow_action',
					'type' => 'message',
					'message' => '<div id="intellipush-messages-sendNow-confirmation" class="ip--display-none"><h2>' . __('Please confirm before sending', 'intellipush') . '</h2><p>' . __('Are you sure you want to send? Please make sure everything is correct.', 'intellipush') . '</p><div class="intellipush-messages-sendNow-status"></div><p class="ip--text-align-right"><a class="acf-button button button-primary intellipush-messages-sendNow-confirmed" href="#">Send now</a></p></div>' .
						'<div class="ip--text-align-right">' .
							'<span class="ip--color-green ip--display-none status status-completed">' . __('Completed', 'intellipush') . '</span>' .
						'<a class="acf-button button button-primary" href="#">' . __('Confirm to send?', 'intellipush') . '</a></div>'
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'intellipush-messages',
					),
				)
			),
			'menu_order' => 10,
		));


		// Message templates
		acf_add_local_field_group(array(
			'key' => 'group_intellipush_messages_templates',
			'title' => __('Templates', 'intellipush'),
			'fields' => array (
				array(
					'key' => 'field_intellipush_messages_templates',
					'name' => 'intellipush_messages_templates',
					'type' => 'repeater',
					'required' => 0,
					'layout' => 'block',
					'button_label' => __('Add template', 'intellipush'),
					'sub_fields' => array(
						array(
							'key' => 'field_intellipush_messages_templates_name',
							'label' => __('Name', 'intellipush'),
							'name' => 'name',
							'type' => 'text',
							'required' => 1
						),
						array(
							'key' => 'field_intellipush_messages_templates_id',
							'label' => __('Id', 'intellipush'),
							'name' => 'id',
							'type' => 'unique_id',
							'hidden' => 1
						),
						array(
							'key' => 'field_intellipush_messages_templates_message',
							'label' => __('Message', 'intellipush') . ' <a href="#TB_inline?width=100%&inlineId=intellipush-messages-shortcodes" class="thickbox ip--font-weight-normal"><small>('.__('shortcodes', 'intellipush').')</small></a>',
							'instructions' => '<small class="ip--sms-counter ip--font-style-normal">About <span class="ip--sms-counter-length">0</span>/159 characters left <small>(<span class="ip--sms-counter-number-of-sms">1</span> SMS per receiver)</small></small>',
							'name' => 'message',
							'type' => 'textarea',
							'required' => 1,
							'rows' => 3,
							'wrapper' => array(
								'class' => 'ip--sms-counter-field'
							)
						)
					)
				)
			),
			'location' => array (
				array (
					array (
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'intellipush-messages',
					),
				)
			),
			'menu_order' => 20,
		));
		add_action('in_admin_footer', function(){
			if ( isset($_GET['page']) && $_GET['page'] === 'intellipush-messages' ) {
				include_once 'pages/messages/footer.php';
			}
		});
	}

	/**
	 * Orverride ACF values
	 *
	 * @since    1.0.0
	 */
	public function acf_update_value( $value, $post_id, $field ) {
		$key = $field['key'];
		$preventSaving = array(
			'field_intellipush_messages_sendNow',
			'field_intellipush_tools_woocommerceOrderExport'
		);
		if ( preg_match('/^'.implode('|',$preventSaving).'/', $key) ) {
			$value = null;
		}
		return $value;
	}

	/**
	 * ACF include field types
	 *
	 * @since    1.0.0
	 */

	public function acf_include_field_types() {
		if (!class_exists('acf_field')){return;}
		require_once( dirname(plugin_dir_path(__FILE__)) . '/includes/class-intellipush-admin-acf-field-types.php' );
		new Intellipush_acf_field_unique_id();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/intellipush-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		add_thickbox();
		wp_enqueue_script(
			$this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/intellipush-admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'acf-pro-input' ),
			$this->version,
			true
		);
	}

	/**
	 * App output buffer
	 *
	 * @since    1.0.0
	 */
	public function app_output_buffer() {
		ob_start();
	}

}
