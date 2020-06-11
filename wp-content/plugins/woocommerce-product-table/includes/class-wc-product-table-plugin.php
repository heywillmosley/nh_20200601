<?php

use Barn2\WPT_Lib\Plugin\Premium_Plugin,
    Barn2\WPT_Lib\Plugin\Licensed_Plugin,
    Barn2\WPT_Lib\Registerable,
    Barn2\WPT_Lib\Translatable,
    Barn2\WPT_Lib\Service_Provider,
    Barn2\WPT_Lib\Util,
    Barn2\Plugin\WC_Product_Table\Ajax_Handler,
    Barn2\Plugin\WC_Product_Table\Table_Shortcode,
    Barn2\Plugin\WC_Product_Table\Cart_Handler,
    Barn2\Plugin\WC_Product_Table\Frontend_Scripts,
    Barn2\Plugin\WC_Product_Table\Template_Handler,
    Barn2\Plugin\WC_Product_Table\Admin\Admin_Controller;

/**
 * The main plugin class. Responsible for setting up to core plugin services.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Plugin extends Premium_Plugin implements Licensed_Plugin, Registerable, Translatable, Service_Provider {

    const NAME    = 'WooCommerce Product Table';
    const ITEM_ID = 12913;

    /* @deprecated - These will be removed in a future version */
    const VERSION = '';
    const FILE    = __FILE__;

    private $services = [];

    public function __construct( $file, $version = '1.0' ) {
        parent::__construct( array(
            'name'               => self::NAME,
            'item_id'            => self::ITEM_ID,
            'version'            => $version,
            'file'               => $file,
            'is_woocommerce'     => true,
            'settings_path'      => 'admin.php?page=wc-settings&tab=products&section=' . WCPT_Settings::SECTION_SLUG,
            'documentation_path' => 'kb-categories/woocommerce-product-table-kb',
            'legacy_db_prefix'   => 'wcpt'
        ) );
    }

    public function register() {
        parent::register();
        add_action( 'plugins_loaded', array( $this, 'maybe_load_plugin' ) );
    }

    public function maybe_load_plugin() {
        // Bail if WooCommerce not installed & active.
        if ( ! Util::is_woocommerce_active() ) {
            return;
        }

        // Includes for template functions and deprecated functions/classes.
        require_once $this->get_dir_path() . 'includes/template-functions.php';
        require_once $this->get_dir_path() . 'includes/compat/deprecated.php';

        add_action( 'init', array( $this, 'load_textdomain' ), 5 );
        add_action( 'init', array( $this, 'load_services' ) );

        if ( $this->get_license()->is_valid() ) {
            add_action( 'widgets_init', array( $this, 'register_widgets' ) );
            add_action( 'after_setup_theme', array( $this, 'load_theme_compat' ), 20 );
            add_action( 'after_setup_theme', array( $this, 'include_back_compat_template_functions' ), 20 ); // After WC loads wc-template-functions.php
        }
    }

    public function load_services() {
        if ( Util::is_admin() ) {
            $this->services['admin'] = new Admin_Controller( $this );
        }

        if ( $this->get_license()->is_valid() && Util::is_front_end() ) {
            $this->services['shortcode']        = new Table_Shortcode();
            $this->services['scripts']          = new Frontend_Scripts( $this->get_version() );
            $this->services['cart_handler']     = new Cart_Handler();
            $this->services['ajax_handler']     = new Ajax_Handler();
            $this->services['template_handler'] = new Template_Handler();
        }

        Util::register_services( $this->services );
    }

    public function load_textdomain() {
        \load_plugin_textdomain( 'woocommerce-product-table', false, \dirname( $this->get_basename() ) . '/languages' );
    }

    public function register_widgets() {
        $widgets_path = $this->get_dir_path() . 'includes/widgets/';

        // Widget includes
        require_once $widgets_path . 'class-wc-product-table-widget.php';
        require_once $widgets_path . 'class-wcpt-widget-layered-nav-filters.php';
        require_once $widgets_path . 'class-wcpt-widget-layered-nav.php';
        require_once $widgets_path . 'class-wcpt-widget-price-filter.php';
        require_once $widgets_path . 'class-wcpt-widget-rating-filter.php';

        $widget_classes = array(
            'WC_Product_Table_Widget_Layered_Nav_Filters',
            'WC_Product_Table_Widget_Layered_Nav',
            'WC_Product_Table_Widget_Price_Filter',
            'WC_Product_Table_Widget_Rating_Filter'
        );

        // Register the product table widgets
        \array_map( 'register_widget', \array_filter( $widget_classes, 'class_exists' ) );
    }

    public function load_theme_compat() {
        require_once $this->get_dir_path() . 'includes/compat/class-wcpt-theme-compat.php';
        WCPT_Theme_Compat::register_theme_compat_hooks();
    }

    public function include_back_compat_template_functions() {
        require_once $this->get_dir_path() . 'includes/compat/back-compat-template-functions.php';
    }

    /**
     * Gets the service by ID.
     *
     * @param string $id The service ID.
     * @return Barn2\WPT_Lib\Service
     */
    public function get_service( $id ) {
        return isset( $this->services[$id] ) ? $this->services[$id] : null;
    }

    /**
     * Gets all services for this plugin.
     *
     * @return array The array of services.
     */
    public function get_services() {
        return (array) $this->services;
    }

    public static function instance() {
        //@todo: Deprecated this function.
        //_deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\wpt()' );
        return \Barn2\Plugin\WC_Product_Table\wpt();
    }

}
