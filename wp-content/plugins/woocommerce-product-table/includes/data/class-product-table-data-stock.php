<?php

/**
 * Gets data for the stock column.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Stock extends Abstract_Product_Table_Data {

    public function get_data() {
        $availability = $this->product->get_availability();

        if ( empty( $availability['availability'] ) && $this->product->is_in_stock() ) {
            $availability['availability'] = __( 'In stock', 'woocommerce-product-table' );
        }
        $stock = '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . $availability['availability'] . '</p>';

        return apply_filters( 'wc_product_table_data_stock', $stock, $this->product );
    }

}
