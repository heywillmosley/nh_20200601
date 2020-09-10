<?php

namespace ACA\WC\Column\Product;

use AC;

/**
 * @since 3.0.3
 */
class Sales extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-wc-product_sales' );
		$this->set_label( __( 'Sales', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $product_id ) {
		$value = $this->get_raw_value( $product_id );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $product_id ) {
		global $wpdb;

		$order_ids = ac_addon_wc_helper()->get_orders_ids_by_product_id( $product_id );
		if ( empty( $order_ids ) ) {
			return $this->get_empty_char();
		}

		$num_orders = $wpdb->get_var( $wpdb->prepare( "
			SELECT 
				SUM( meta_value )
			FROM 
				{$wpdb->prefix}woocommerce_order_itemmeta
			WHERE 
				meta_key = '_qty'
			 	AND 
			 	order_item_id IN (
					SELECT DISTINCT( wc_oim.order_item_id )
					FROM {$wpdb->prefix}woocommerce_order_itemmeta wc_oim
					INNER JOIN {$wpdb->prefix}woocommerce_order_items as wc_oi
					ON wc_oim.order_item_id = wc_oi.order_item_id
					WHERE wc_oim.meta_key = '_product_id'
					AND wc_oim.meta_value = %d
					AND order_id IN (" . implode( ',', $order_ids ) . ")
				)",
			$product_id
		) );

		if ( ! $num_orders ) {
			return false;
		}

		return $num_orders;
	}

}