<?php

/**
 * Gets data for the price column.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Price extends Abstract_Product_Table_Data {

    public function get_data() {
        return apply_filters( 'wc_product_table_data_price', $this->product->get_price_html(), $this->product );
    }

    public function get_sort_data() {
        $price = floatval( $this->product->get_price() );
        return $price ? number_format( $price, 2, '.', '' ) : '0.00';
    }

}
