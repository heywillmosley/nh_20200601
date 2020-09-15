<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       heywillmosley.com
 * @since      1.0.0
 *
 * @package    Wp_Page_Handbook
 * @subpackage Wp_Page_Handbook/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Page_Handbook
 * @subpackage Wp_Page_Handbook/includes
 * @author     Will Mosley <sales@heywillmosley.com>
 */
class Wp_Page_Handbook_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-page-handbook',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
