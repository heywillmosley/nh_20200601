<?php

namespace Barn2\Plugin\WC_Product_Table\Admin;

use Barn2\WPT_Lib\Util,
    Barn2\WPT_Lib\Plugin\Licensed_Plugin,
    Barn2\WPT_Lib\Registerable,
    Barn2\WPT_Lib\Service,
    Barn2\WPT_Lib\Plugin\Admin\Admin_Links;

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Service {

    private $plugin;
    private $services;

    public function __construct( Licensed_Plugin $plugin ) {
        $this->plugin   = $plugin;
        $this->services = [
            new Settings_Page( $plugin ),
            new TinyMCE(),
            new Admin_Links( $plugin )
        ];
    }

    public function register() {
        Util::register_services( $this->services );

        \add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
    }

    public function register_admin_scripts( $hook_suffix ) {
        if ( 'woocommerce_page_wc-settings' !== $hook_suffix ) {
            return;
        }

        $suffix = Util::get_script_suffix();

        \wp_enqueue_style( 'wcpt-admin', \WCPT_Util::get_asset_url( "css/admin/wc-product-table-admin{$suffix}.css" ), array(), $this->plugin->get_version() );
        \wp_enqueue_script( 'wcpt-admin', \WCPT_Util::get_asset_url( "js/admin/wc-product-table-admin{$suffix}.js" ), array( 'jquery' ), $this->plugin->get_version(), true );
    }

}
