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


// Define path and URL to the ACF plugin.
define( 'MY_ACF_PATH', plugin_dir_path( __FILE__ ) . 'includes/acf/' );
define( 'MY_ACF_URL', plugin_dir_url( __FILE__ ) . '/includes/acf/' );

// Include the ACF plugin.
include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
//add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
function my_acf_settings_show_admin( $show_admin ) {
    return false;
}


if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5f5f8721bdd69',
	'title' => 'Documents',
	'fields' => array(
		array(
			'key' => 'field_5f63d088a0a71',
			'label' => 'Logo',
			'name' => 'logo',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => 'jpg, jpeg, png, gif',
		),
		array(
			'key' => 'field_5f63cd23218e8',
			'label' => 'Custom Cover',
			'name' => 'cover',
			'type' => 'file',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'library' => 'all',
			'min_size' => '',
			'max_size' => 10,
			'mime_types' => 'png, jpeg, jpg',
		),
		array(
			'key' => 'field_5f5fa9dc7ea34',
			'label' => 'Introduction Content',
			'name' => 'introduction_content',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'visual',
			'toolbar' => 'basic',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_5f5faa1b7ea35',
			'label' => 'Body Content',
			'name' => 'body_content',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'visual',
			'toolbar' => 'basic',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_5f638dbafe86e',
			'label' => 'Included Content',
			'name' => 'included_content',
			'type' => 'checkbox',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Posts' => 'Posts',
				'Pages' => 'Pages',
				'Products' => 'Products',
				'Ingredients' => 'Ingredients',
			),
			'allow_custom' => 0,
			'default_value' => array(
			),
			'layout' => 'horizontal',
			'toggle' => 0,
			'return_format' => 'value',
			'save_custom' => 0,
		),
		array(
			'key' => 'field_5f5f9794b857b',
			'label' => 'Pages',
			'name' => 'pages',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5f638dbafe86e',
						'operator' => '==',
						'value' => 'Pages',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'page',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
			),
			'elements' => '',
			'min' => '',
			'max' => '',
			'return_format' => 'id',
		),
		array(
			'key' => 'field_5f5f9802b857c',
			'label' => 'Posts',
			'name' => 'posts',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5f638dbafe86e',
						'operator' => '==',
						'value' => 'Posts',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'post',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
			),
			'elements' => '',
			'min' => '',
			'max' => '',
			'return_format' => 'id',
		),
		array(
			'key' => 'field_5f639fb4fe870',
			'label' => 'Product Content',
			'name' => 'product_content',
			'type' => 'select',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5f638dbafe86e',
						'operator' => '==',
						'value' => 'Products',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Category(s)' => 'Category(s)',
				'Individual Item(s)' => 'Individual Item(s)',
			),
			'default_value' => false,
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),
		array(
			'key' => 'field_5f5f9817b857d',
			'label' => 'Individual Product(s)',
			'name' => 'products',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5f639fb4fe870',
						'operator' => '==',
						'value' => 'Individual Item(s)',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'product',
				1 => 'product_variation',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
				1 => 'post_type',
			),
			'elements' => '',
			'min' => '',
			'max' => '',
			'return_format' => 'id',
		),
		array(
			'key' => 'field_5f63a078fe871',
			'label' => 'Product Categories',
			'name' => 'products_categories',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5f638dbafe86e',
						'operator' => '==',
						'value' => 'Products',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'product_cat',
			'field_type' => 'multi_select',
			'allow_null' => 0,
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'multiple' => 0,
		),
		array(
			'key' => 'field_5f639e75fe86f',
			'label' => 'Ingredients',
			'name' => 'ingredients',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5f638dbafe86e',
						'operator' => '==',
						'value' => 'Ingredients',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'page',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
				1 => 'post_type',
			),
			'elements' => '',
			'min' => '',
			'max' => '',
			'return_format' => 'id',
		),
		array(
			'key' => 'field_5f5faa397ea36',
			'label' => 'Closing Content',
			'name' => 'closing_content',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'visual',
			'toolbar' => 'basic',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_5f5fab9e13569',
			'label' => 'Paper Size',
			'name' => 'paper_size',
			'type' => 'radio',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Letter (8.5" x 11")' => 'Letter (8.5" x 11")',
				'Tabloid (11" x 17")' => 'Tabloid (11" x 17")',
				'Legal (8.5" x 14")' => 'Legal (8.5" x 14")',
				'Statement (5.5" x 8.5")' => 'Statement (5.5" x 8.5")',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'default_value' => 'Letter (8.5" x 11")',
			'layout' => 'vertical',
			'return_format' => 'value',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_5f5faa757ea37',
			'label' => 'Settings',
			'name' => 'settings',
			'type' => 'checkbox',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Table of Contents' => 'Table of Contents',
				'Index' => 'Index',
				'Page Numbers' => 'Page Numbers',
			),
			'allow_custom' => 0,
			'default_value' => array(
			),
			'layout' => 'vertical',
			'toggle' => 0,
			'return_format' => 'value',
			'save_custom' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'document',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'the_content',
		1 => 'excerpt',
		2 => 'discussion',
		3 => 'comments',
		4 => 'revisions',
		5 => 'slug',
		6 => 'author',
		7 => 'format',
		8 => 'page_attributes',
		9 => 'featured_image',
		10 => 'categories',
		11 => 'tags',
		12 => 'send-trackbacks',
	),
	'active' => true,
	'description' => '',
));

endif;


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
