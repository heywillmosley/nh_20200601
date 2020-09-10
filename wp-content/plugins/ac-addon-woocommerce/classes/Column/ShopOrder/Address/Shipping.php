<?php

namespace ACA\WC\Column\ShopOrder\Address;

use ACA\WC\Column\ShopOrder\Address;
use ACA\WC\Settings;
use WC_Order;

/**
 * @since 3.0
 */
class Shipping extends Address {

	public function __construct() {
		$this->set_type( 'column-wc-order_shipping_address' );
		$this->set_label( __( 'Shipping Address', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		if ( ! $this->get_address_property() ) {
			return false;
		}

		return '_shipping_' . $this->get_address_property();
	}

	protected function get_formatted_address( WC_Order $order ) {
		return $order->get_formatted_shipping_address();
	}

	public function get_setting_address_object() {
		return new Settings\Address( $this );
	}

}