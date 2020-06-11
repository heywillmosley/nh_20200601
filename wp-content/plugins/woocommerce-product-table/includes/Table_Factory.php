<?php

namespace Barn2\Plugin\WC_Product_Table;

use Barn2\Plugin\WC_Product_Table\Table_Cache;

/**
 * WC_Product_Table factory class.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Factory {

    private static $tables     = array();
    private static $current_id = 1;

    /**
     * Create a new table based on the supplied args.
     *
     * @param array $args The args to use for the table.
     * @return WC_Product_Table The product table object.
     */
    public static function create( $args ) {
        // Merge in the default args, so our table ID reflects the full list of args, including settings page.
        $args = \wp_parse_args( $args, \WC_Product_Table_Args::get_defaults() );
        $id   = self::generate_id( $args );

        $table             = new \WC_Product_Table( $id, $args );
        self::$tables[$id] = $table;

        return $table;
    }

    /**
     * Fetch an existing table by ID.
     *
     * @param string $id The product table ID.
     * @return WC_Product_Table The product table object.
     */
    public static function fetch( $id ) {
        if ( empty( $id ) ) {
            return false;
        }

        $table = false;

        if ( isset( self::$tables[$id] ) ) {
            $table = self::$tables[$id];
        } elseif ( $table = Table_Cache::get_table( $id ) ) {
            self::$tables[$id] = $table;
        }

        return $table;
    }

    private static function generate_id( $args ) {
        $id = 'wcpt_' . \substr( \md5( \serialize( $args ) ), 0, 16 ) . '_' . self::$current_id;
        self::$current_id ++;

        return $id;
    }

}
