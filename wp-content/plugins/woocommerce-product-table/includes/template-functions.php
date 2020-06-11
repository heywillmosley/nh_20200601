<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Barn2\Plugin\WC_Product_Table\Table_Factory;

/**
 * Template functions for WooCommerce Product Table.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
if ( ! function_exists( 'wc_product_table' ) ) {

    /**
     * Global namespace version of Barn2\Plugin\WC_Product_Table\wpt(). Returns the shared plugin instance.
     *
     * @return WC_Product_Table_Plugin The plugin instance.
     * @deprecated 2.6.2 Use Barn2\Plugin\WC_Product_Table->wpt()
     */
    function wc_product_table() {
        _deprecated_function( __FUNCTION__, '2.6.2', 'Barn2\\Plugin\\WC_Product_Table->wpt()' );
        return \Barn2\Plugin\WC_Product_Table\wpt();
    }

}

if ( ! function_exists( 'wc_get_product_table' ) ) {

    /**
     * Returns a new product table with the specified arguments.
     *
     * @param array $args The table arguments.
     * @return string The product table HTML.
     */
    function wc_get_product_table( $args = array() ) {
        // Create and return the table as HTML
        $table = Table_Factory::create( $args );
        return $table->get_table( 'html' );
    }

}

if ( ! function_exists( 'wc_the_product_table' ) ) {

    /**
     * Prints (echos) a product table with the specified arguments.
     *
     * @param array $args The table arguments.
     */
    function wc_the_product_table( $args = array() ) {
        echo wc_get_product_table( $args );
    }

}
