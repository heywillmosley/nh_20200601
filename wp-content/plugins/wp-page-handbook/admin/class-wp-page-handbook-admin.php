<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       heywillmosley.com
 * @since      1.0.0
 *
 * @package    Wp_Page_Handbook
 * @subpackage Wp_Page_Handbook/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Page_Handbook
 * @subpackage Wp_Page_Handbook/admin
 * @author     Will Mosley <sales@heywillmosley.com>
 */
class Wp_Page_Handbook_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Page_Handbook_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Page_Handbook_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-page-handbook-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Page_Handbook_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Page_Handbook_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-page-handbook-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registers document post type.
	 *
	 * @since     1.0.0
	 * @return    string    Registers post type
	 * @uses register_post_type()
	 */
	 public static function new_cpt_document() {
		 $cap_type = 'post';
		 $plural = 'Documents';
		 $single = 'Document';
		 $cpt_name = 'document';
		 $opts['can_export'] = TRUE;
		 $opts['capability_type'] = $cap_type;
		 $opts['description'] = '';
		 $opts['exclude_from_search'] = FALSE;
		 $opts['has_archive'] = FALSE;
		 $opts['hierarchical'] = FALSE;
		 $opts['map_meta_cap'] = TRUE;
		 $opts['menu_icon'] = 'dashicons-format-aside';
		 $opts['menu_position'] = 20;
		 $opts['public'] = TRUE;
		 $opts['publicly_querable'] = TRUE;
		 $opts['query_var'] = TRUE;
		 $opts['register_meta_box_cb'] = '';
		 $opts['rewrite'] = FALSE;
		 $opts['show_in_admin_bar'] = TRUE;
		 $opts['show_in_menu'] = TRUE;
		 $opts['show_in_nav_menu'] = TRUE;

		 $opts['labels']['add_new'] = esc_html__( "Add New {$single}", 'document' );
		 $opts['labels']['add_new_item'] = esc_html__( "Add New {$single}", 'document' );
		 $opts['labels']['all_items'] = esc_html__( $plural, 'document' );
		 $opts['labels']['edit_item'] = esc_html__( "Edit {$single}" , 'document' );
		 $opts['labels']['menu_name'] = esc_html__( $plural, 'document' );
		 $opts['labels']['name'] = esc_html__( $plural, 'document' );
		 $opts['labels']['name_admin_bar'] = esc_html__( $single, 'document' );
		 $opts['labels']['new_item'] = esc_html__( "New {$single}", 'document' );
		 $opts['labels']['not_found'] = esc_html__( "No {$plural} Found", 'document' );
		 $opts['labels']['not_found_in_trash'] = esc_html__( "No {$plural} Found in Trash", 'document' );
		 $opts['labels']['parent_item_colon'] = esc_html__( "Parent {$plural} :", 'document' );
		 $opts['labels']['search_items'] = esc_html__( "Search {$plural}", 'document' );
		 $opts['labels']['singular_name'] = esc_html__( $single, 'document' );
		 $opts['labels']['view_item'] = esc_html__( "View {$single}", 'document' );
		 register_post_type( strtolower( $cpt_name ), $opts );
	 }


	 public function my_acf_add_local_field_groups() {

		acf_add_local_field_group(array(
			'key' => 'group_1',
			'title' => 'My Group',
			'fields' => array (
				array (
					'key' => 'field_1',
					'label' => 'Sub Title',
					'name' => 'sub_title',
					'type' => 'text',
				)
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
					),
				),
			),
		));

	} // end method my_acf_add_local_field_groups()

} // end class
