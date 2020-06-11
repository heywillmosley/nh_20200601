<?php

/**
 * Gets data for the name column.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Name extends Abstract_Product_Table_Data {

    public function get_data() {
        $name = WCPT_Util::get_product_name( $this->product );

        if ( array_intersect( array( 'all', 'name' ), $this->links ) ) {
            $name = WCPT_Util::format_product_link( $this->product, $name );
        }
        return apply_filters( 'wc_product_table_data_name', $name, $this->product );
    }

}
