<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.intellipush.com
 * @since             1.0.0
 * @package           Intellipush
 *
 * @wordpress-plugin
 * Plugin Name:       Intellipush
 * Plugin URI:        https://www.intellipush.com/en/
 * Description:       Intellipush for Wordpress (Mobile marketing, professional SMS communication)
 * Version:           2.0.0
 * Author:            Netron
 * Author URI:        https://netron.no/en/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       intellipush
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'INTELLIPUSH_VERSION', '2.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-intellipush-activator.php
 */
function activate_intellipush() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-intellipush-activator.php';
	Intellipush_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-intellipush-deactivator.php
 */
function deactivate_intellipush() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-intellipush-deactivator.php';
	Intellipush_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_intellipush' );
register_deactivation_hook( __FILE__, 'deactivate_intellipush' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-intellipush.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_intellipush() {

	$plugin = new Intellipush();
	$plugin->run();

}
run_intellipush();
