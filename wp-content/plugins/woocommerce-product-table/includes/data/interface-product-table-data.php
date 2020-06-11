<?php

/**
 * Interface for the product table data.
 *
 * Each column in the product table implements this interface to retrieve its data.
 *
 * @package   Barn2/woocommerce-product-table
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface Product_Table_Data {

    public function get_data();

    public function get_filter_data();

    public function get_sort_data();

}
