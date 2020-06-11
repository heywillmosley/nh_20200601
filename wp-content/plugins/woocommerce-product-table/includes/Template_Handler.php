<?php

namespace Barn2\Plugin\WC_Product_Table;

use Barn2\WPT_Lib\Registerable,
    Barn2\WPT_Lib\Service;

/**
 * This class handles adding the product table to the shop, archive, and product search pages.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Template_Handler implements Registerable, Service {

    public function register() {
        \add_action( 'template_redirect', array( __CLASS__, 'template_shop_override' ) );
    }

    public static function template_shop_override() {
        $misc_setings = \WCPT_Settings::get_setting_misc();
        $override     = false;

        if ( ! empty( $misc_setings['shop_override'] ) && \is_shop() ) {
            $override = true;
        }

        if ( ! empty( $misc_setings['archive_override'] ) && \is_product_category() && ! \is_shop() ) {
            $override = true;
        }

        if ( $override == true ) {
            \add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'disable_default_woocommerce_loop' ) );
            \add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'add_product_table_after_shop_loop' ) );

            $theme    = \wp_get_theme();
            $template = $theme->get( 'Template' );
            $name     = $theme->get( 'Name' );

            if ( $template == 'genesis' || $name == 'Genesis' ) {
                //Replace Genesis loop with product table
                \remove_action( 'genesis_loop', 'genesis_do_loop' );
                \add_action( 'genesis_loop', array( __CLASS__, 'add_product_table_after_shop_loop' ) );
            }
        }
    }

    public static function disable_default_woocommerce_loop() {
        $GLOBALS['woocommerce_loop']['total'] = false;
    }

    public static function add_product_table_after_shop_loop() {
        $shortcode = '[product_table]';

        $args = \shortcode_parse_atts( \str_replace( array( '[product_table', ']' ), '', $shortcode ) );
        $args = ! empty( $args ) && \is_array( $args ) ? $args : array();

        if ( \is_product_category() ) {
            // Product category archive
            $args['category'] = \get_queried_object_id();
        } elseif ( \is_product_tag() ) {
            // Product tag archive
            $args['tag'] = \get_queried_object_id();
        } elseif ( \is_product_taxonomy() ) {
            // Other product taxonomy archive
            $term         = \get_queried_object();
            $args['term'] = "{$term->taxonomy}:{$term->term_id}";
        } elseif ( \is_post_type_archive( 'product' ) && ( $search_term = \get_query_var( 's' ) ) ) {
            // Product search results page
            $args['search_term'] = $search_term;
        }

        // Display the product table
        \wc_the_product_table( $args );
    }

}
