<?php

/**
 * Provides backwards compatibility functions for older versions of WordPress and WooCommerce.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @deprecated 2.6.2 Use Barn2\Plugin\WC_Product_Table\Table_Shortcode
 */
class WC_Product_Table_Shortcode {

    /**
     * @deprecated
     */
    const SHORTCODE = 'product_table';

    public static function __callStatic( $name, $args ) {
        if ( method_exists( 'Barn2\\Plugin\\WC_Product_Table\\Table_Shortcode', $name ) ) {
            _deprecated_function( "WC_Product_Table_Shortcode::{$name}()", '2.6.2', "Barn2\\Plugin\\WC_Product_Table\\Table_Shortcode::{$name}()" );
            return call_user_func_array( array( 'Barn2\\Plugin\\WC_Product_Table\\Table_Shortcode', $name ), $args );
        }
    }

}

/**
 * @deprecated 2.6.2 Use Barn2\Plugin\WC_Product_Table\Frontend_Scripts
 */
class WC_Product_Table_Frontend_Scripts {

    const SCRIPT_HANDLE = \Barn2\Plugin\WC_Product_Table\Frontend_Scripts::SCRIPT_HANDLE;

    public function __construct( $script_version ) {
        _deprecated_function( __FUNCTION__, '2.6.2', 'new Barn2\\Plugin\\WC_Product_Table\\Frontend_Scripts()' );
    }

    public function __call( $name, $args ) {
        $scripts = \Barn2\Plugin\WC_Product_Table\wpt()->get_service( 'scripts' );

        if ( $scripts && method_exists( $scripts, $name ) ) {
            _deprecated_function( "WC_Product_Table_Frontend_Scripts->{$name}()", '2.6.2', "Barn2\\Plugin\\WC_Product_Table\\Frontend_Scripts->{$name}()" );
            return call_user_func_array( array( $scripts, $name ), $args );
        }
    }

    public static function __callStatic( $name, $args ) {
        // Back compat for previous static methods used for loading scripts.
        if ( in_array( $name, [ 'register_styles', 'register_scripts', 'load_scripts' ] ) ) {
            _deprecated_function( "WC_Product_Table_Frontend_Scripts::{$name}()", '2.6', "non-static function Barn2\\Plugin\\WC_Product_Table\\Frontend_Scripts->{$name}()" );

            $scripts = \Barn2\Plugin\WC_Product_Table\wpt()->get_service( 'scripts' );

            if ( $scripts && method_exists( $scripts, $name ) ) {
                return call_user_func_array( array( $scripts, $name ), $args );
            }
        } elseif ( method_exists( 'Barn2\\Plugin\\WC_Product_Table\\Frontend_Scripts', $name ) ) {
            _deprecated_function( "WC_Product_Table_Frontend_Scripts::{$name}()", '2.6.2', "Barn2\\Plugin\\WC_Product_Table\\Frontend_Scripts::{$name}()" );
            return call_user_func_array( array( 'Barn2\\Plugin\\WC_Product_Table\\Frontend_Scripts', $name ), $args );
        }
    }

}

/**
 * @deprecated 2.6.2 Use Barn2\Plugin\WC_Product_Table\Table_Cache
 */
class WC_Product_Table_Cache {

    const TABLE_CACHE_EXPIRY = \Barn2\Plugin\WC_Product_Table\Table_Cache::TABLE_CACHE_EXPIRY;

    public static function get_table( $id ) {
        _deprecated_function( "WC_Product_Table_Cache::get_table()", '2.6.2', "Barn2\\Plugin\\WC_Product_Table\\Table_Cache::get_table()" );
        return \Barn2\Plugin\WC_Product_Table\Table_Cache::get_table( $id );
    }

}

/**
 * @deprecated 2.6.2 Use Barn2\Plugin\WC_Product_Table\Hook_Manager
 */
class WC_Product_Table_Hook_Manager {

    private $hook_obj;

    public function __construct( $args ) {
        _deprecated_function( __FUNCTION__, '2.6.2', 'Barn2\\Plugin\\WC_Product_Table\\Hook_Manager' );
        $this->hook_obj = new \Barn2\Plugin\WC_Product_Table\Hook_Manager( $args );
    }

    public function __call( $name, $args ) {
        if ( method_exists( $this->hook_obj, $name ) ) {
            _deprecated_function( "WC_Product_Table_Hook_Manager->{$name}()", '2.6.2', "Barn2\\Plugin\\WC_Product_Table\\Hook_Manager->{$name}()" );
            return call_user_func_array( array( $this->hook_obj, $name ), $args );
        }
    }

    public static function __callStatic( $name, $args ) {
        if ( method_exists( 'Barn2\\Plugin\\WC_Product_Table\\Hook_Manager', $name ) ) {
            _deprecated_function( "WC_Product_Table_Hook_Manager::{$name}()", '2.6.2', "Barn2\\Plugin\\WC_Product_Table\\Hook_Manager::{$name}()" );
            return call_user_func_array( array( 'Barn2\\Plugin\\WC_Product_Table\\Hook_Manager', $name ), $args );
        }
    }

}

/**
 * @deprecated 2.6 Use Barn2\Plugin\WC_Product_Table\Ajax_Handler
 */
class WC_Product_Table_Ajax_Handler {

    public static function load_products() {
        _deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\Ajax_Handler->load_products()' );
    }

    public static function add_to_cart() {
        _deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\Ajax_Handler->add_to_cart()' );
    }

    public static function add_to_cart_multi() {
        _deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\Ajax_Handler->add_to_cart_multi()' );
    }

}

/**
 * @deprecated 2.6 Use Barn2\Plugin\WC_Product_Table\Cart_Handler
 */
class WC_Product_Table_Cart_Handler {

    public static function process_multi_cart() {
        _deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\Cart_Handler->process_multi_cart()' );
    }

    public static function __callStatic( $name, $args ) {
        if ( method_exists( 'Barn2\\Plugin\\WC_Product_Table\\Cart_Handler', $name ) ) {
            _deprecated_function( "WC_Product_Table_Cart_Handler::{$name}()", '2.6', "Barn2\\Plugin\\WC_Product_Table\\Cart_Handler::{$name}()" );
            return call_user_func_array( array( 'Barn2\\Plugin\\WC_Product_Table\\Cart_Handler', $name ), $args );
        }
    }

}

/**
 * @deprecated 2.6 Use Barn2\Plugin\WC_Product_Table\Table_Factory
 */
class WC_Product_Table_Factory {

    public static function create( $args ) {
        _deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\Table_Factory::create()' );
        return Barn2\Plugin\WC_Product_Table\Table_Factory::create( $args );
    }

    public static function fetch( $id ) {
        _deprecated_function( __FUNCTION__, '2.6', 'Barn2\\Plugin\\WC_Product_Table\\Table_Factory::fetch()' );
        return Barn2\Plugin\WC_Product_Table\Table_Factory::fetch( $id );
    }

}

if ( ! function_exists( 'wcpt_back_compat_args' ) ) {

    /**
     * Maintain support for old attribute names.
     *
     * @param array $args The array of product table attributes
     * @return array The updated attributes with old ones replaced with their new equivalent
     * @deprecated 2.6
     */
    function wcpt_back_compat_args( $args ) {
        _deprecated_function( __FUNCTION__, '2.6' );
        //@todo: Remove this in a future update and make function in Table_Shortcode::check_legacy_atts() private.
        return \Barn2\Plugin\WC_Product_Table\Table_Shortcode::check_legacy_atts( $args );
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_parent' ) ) {

    /**
     * @deprecated 2.6 Use WCPT_Util::get_parent( $product )
     */
    function wcpt_get_parent( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', 'WCPT_Util::get_parent( $product )' );
        return WCPT_Util::get_parent( $product );
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_parent_id' ) ) {

    /**
     * @deprecated 2.6 Use $product->get_parent_id()
     */
    function wcpt_get_parent_id( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', '$product->get_parent_id()' );

        $parent_id = false;

        if ( method_exists( $product, 'get_parent_id' ) ) {
            $parent_id = $product->get_parent_id();
        } elseif ( method_exists( $product, 'get_parent' ) ) {
            $parent_id = $product->get_parent();
        }
        return $parent_id;
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_name' ) ) {

    /**
     * @deprecated 2.6 Use WCPT_Util::get_product_name( $product )
     */
    function wcpt_get_name( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', 'WCPT_Util::get_product_name( $product )' );
        return WCPT_Util::get_product_name( $product );
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_description' ) ) {

    /**
     * @deprecated 2.6 Use $product->get_description()
     */
    function wcpt_get_description( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', '$product->get_description()' );

        if ( method_exists( $product, 'get_description' ) ) {
            return $product->get_description();
        } else {
            $post = WCPT_Util::get_post( $product );
            return $post->post_content;
        }
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_dimensions' ) ) {

    /**
     * @deprecated 2.6 Use wc_format_dimensions( $product->get_dimensions( false ) )
     */
    function wcpt_get_dimensions( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', 'wc_format_dimensions( $product->get_dimensions( false ) )' );

        if ( function_exists( 'wc_format_dimensions' ) ) {
            return wc_format_dimensions( $product->get_dimensions( false ) );
        }
        return $product->get_dimensions();
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_stock_status' ) ) {

    /**
     * @deprecated 2.6 Use $product->get_stock_status()
     */
    function wcpt_get_stock_status( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', '$product->get_stock_status()' );

        if ( method_exists( $product, 'get_stock_status' ) ) {
            return $product->get_stock_status();
        }
        return $product->stock_status;
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_min_purchase_quantity' ) ) {

    /**
     * @deprecated 2.6 Use $product->get_min_purchase_quantity()
     */
    function wcpt_get_min_purchase_quantity( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', '$product->get_min_purchase_quantity()' );

        if ( method_exists( $product, 'get_min_purchase_quantity' ) ) {
            return $product->get_min_purchase_quantity();
        }
        return 1;
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_max_purchase_quantity' ) ) {

    /**
     * @deprecated 2.6 Use $product->get_stock_quantity()
     */
    function wcpt_get_max_purchase_quantity( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', '$product->get_stock_quantity()' );

        return $product->backorders_allowed() ? '' : $product->get_stock_quantity();
    }

}

// WC < 3.0
if ( ! function_exists( 'wcpt_woocommerce_quantity_input' ) ) {

    /**
     * @deprecated 2.6 Use woocommerce_quantity_input()
     */
    function wcpt_woocommerce_quantity_input( $product ) {
        _deprecated_function( __FUNCTION__, '2.6', 'woocommerce_quantity_input()' );

        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            if ( ! $product->is_sold_individually() ) {
                woocommerce_quantity_input( array(
                    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
                    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
                    'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
                ) );
            }
        } else {
            woocommerce_quantity_input( array(
                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity()
            ) );
        }
    }

}

