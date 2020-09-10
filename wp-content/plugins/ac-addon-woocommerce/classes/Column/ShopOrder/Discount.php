<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Filtering;
use ACP;

/**
 * @since 1.0
 */
class Discount extends ACP\Column\Meta {

	public function __construct() {
		$this->set_type( 'column-wc-order_discount' );
		$this->set_label( __( 'Order Discount', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_cart_discount';
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		if ( ! $order->get_total_discount() ) {
			return $this->get_empty_char();
		}

		return $order->get_discount_to_display();
	}

	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		return $order->get_total_discount();
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return new ACP\Editing\Model\Disabled( $this );
	}

}