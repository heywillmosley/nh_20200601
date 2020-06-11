<?php

/**
 * Gets data for the dimensions column.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Dimensions extends Abstract_Product_Table_Data {

    public function get_data() {
        $dimensions = $this->product->has_dimensions() ? wc_format_dimensions( $this->product->get_dimensions( false ) ) : '';
        return apply_filters( 'wc_product_table_data_dimensions', $dimensions, $this->product );
    }

}
