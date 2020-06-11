<?php

/**
 * Gets data for the SKU column.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Sku extends Abstract_Product_Table_Data {

    public function get_data() {
        $sku = $this->product->get_sku();

        if ( $sku && array_intersect( array( 'all', 'sku' ), $this->links ) ) {
            $sku = WCPT_Util::format_product_link( $this->product, $sku );
        }
        return apply_filters( 'wc_product_table_data_sku', $sku, $this->product );
    }

}
