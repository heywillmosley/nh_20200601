<?php

/**
 * Provides backwards compatibility for template functions in older versions of WooCommerce.
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

if ( ! function_exists( 'woocommerce_product_loop' ) ) {

    /**
     * Should the WooCommerce loop be displayed?
     *
     * This will return true if we have posts (products) or if we have subcats to display.
     *
     * @since 3.4.0
     * @return bool
     */
    function woocommerce_product_loop() {
        return have_posts() || ( function_exists( 'woocommerce_get_loop_display_mode' ) && 'products' !== woocommerce_get_loop_display_mode() );
    }

}