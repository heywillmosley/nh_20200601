<?php

namespace Barn2\Plugin\WC_Product_Table\Admin;

use Barn2\WPT_Lib\Registerable,
    Barn2\WPT_Lib\Service,
    Barn2\WPT_Lib\Util;

/**
 * This class handles our TinyMCE toolbar button.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class TinyMCE implements Registerable, Service {

    public function register() {
        if ( ! \current_user_can( 'edit_posts' ) && ! \current_user_can( 'edit_pages' ) ) {
            return;
        }

        if ( 'true' !== \get_user_option( 'rich_editing' ) ) {
            return;
        }

        \add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
        \add_filter( 'mce_buttons_2', array( $this, 'add_tinymce_button' ) );
    }

    public function add_tinymce_plugin( $plugins ) {
        $plugins['producttable'] = \WCPT_Util::get_asset_url( 'js/admin/tinymce-product-table' . Util::get_script_suffix() . '.js' );
        return $plugins;
    }

    public function add_tinymce_button( $buttons ) {
        \array_push( $buttons, 'producttable' );
        return $buttons;
    }

}
