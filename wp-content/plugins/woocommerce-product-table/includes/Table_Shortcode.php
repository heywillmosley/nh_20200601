<?php

namespace Barn2\Plugin\WC_Product_Table;

use Barn2\WPT_Lib\Registerable,
    Barn2\WPT_Lib\Service;

/**
 * This class handles our product table shortcode.
 *
 * Example usage:
 *   [product_table
 *       columns="name,description,price,add-to-cart"
 *       category="t-shirts",
 *       tag="cool"]
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Shortcode implements Registerable, Service {

    const SHORTCODE = 'product_table';

    public function register() {
        self::register_shortcode();
    }

    public static function register_shortcode() {
        \add_shortcode( self::SHORTCODE, array( __CLASS__, 'do_shortcode' ) );
    }

    /**
     * Handles our product table shortcode.
     *
     * @param array $atts The attributes passed in to the shortcode
     * @param string $content The content passed to the shortcode (not used)
     * @return string The shortcode output
     */
    public static function do_shortcode( $atts, $content = '' ) {
        if ( ! self::can_do_shortocde() ) {
            return '';
        }

        // Fill-in missing attributes, and ensure back compat for old attribute names.
        $r = \shortcode_atts( \WC_Product_Table_Args::get_defaults(), self::check_legacy_atts( $atts ), self::SHORTCODE );

        // Return the table as HTML
        $output = \apply_filters( 'wc_product_table_shortcode_output', \wc_get_product_table( $r ) );

        return $output;
    }

    private static function can_do_shortocde() {
        // Don't run in the search results.
        if ( \is_search() && \in_the_loop() && ! \apply_filters( 'wc_product_table_run_in_search', false ) ) {
            return false;
        }

        return true;
    }

    /**
     * Maintain support for old shortcode attributes.
     *
     * @param array $args The array of product table attributes
     * @return array The updated attributes with old ones replaced with their new equivalent
     */
    public static function check_legacy_atts( $args ) {
        if ( empty( $args ) ) {
            return $args;
        }

        $compat = array(
            'add-to-cart'          => 'cart_button',
            'add_to_cart'          => 'cart_button',
            'display_page_length'  => 'page_length',
            'display_totals'       => 'totals',
            'display_pagination'   => 'pagination',
            'display_search_box'   => 'search_box',
            'display_reset_button' => 'reset_button',
            'show_quantities'      => 'show_quantity'
        );

        foreach ( $compat as $old => $new ) {
            if ( isset( $args[$old] ) ) {
                $args[$new] = $args[$old];
                unset( $args[$old] );
            }
        }

        return $args;
    }

}
