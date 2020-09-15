<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              heywillmosley.com
 * @since             1.0.0
 * @package           Wp_Page_Handbook
 *
 * @wordpress-plugin
 * Plugin Name:       WP Page Handbook
 * Plugin URI:        thenewhuman.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Will Mosley
 * Author URI:        heywillmosley.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-page-handbook
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
define( 'WP_PAGE_HANDBOOK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-page-handbook-activator.php
 */
function activate_wp_page_handbook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-page-handbook-activator.php';
	Wp_Page_Handbook_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-page-handbook-deactivator.php
 */
function deactivate_wp_page_handbook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-page-handbook-deactivator.php';
	Wp_Page_Handbook_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_page_handbook' );
register_deactivation_hook( __FILE__, 'deactivate_wp_page_handbook' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-page-handbook.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_page_handbook() {

	$plugin = new Wp_Page_Handbook();
	define( 'WP_PAGE_HANDBOOK_NAME', $plugin->get_plugin_name() );
	$plugin->run();

}
run_wp_page_handbook();
