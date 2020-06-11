<?php

namespace Barn2\Plugin\WC_Product_Table;

/**
 * Factory to create/return the shared plugin instance.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Factory {

    private static $plugin = null;

    /**
     * Create/return the shared plugin instance.
     *
     * @param string $file
     * @param string $version
     * @return Barn2\Plugin\WC_Private_Store\Plugin
     */
    public static function create( $file, $version ) {
        if ( null === self::$plugin ) {
            self::$plugin = new \WC_Product_Table_Plugin( $file, $version );
        }
        return self::$plugin;
    }

}
