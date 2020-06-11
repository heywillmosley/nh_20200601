<?php

/**
 * Gets data for the tags column.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Tags extends Abstract_Product_Table_Data {

    public function get_data() {
        return apply_filters( 'wc_product_table_data_tags', $this->get_product_taxonomy_terms( 'tags' ), $this->product );
    }

}
