<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC;
use ACA\WC\Filtering;
use ACP;

/**
 * @since 1.4
 */
class ShippingMethod extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-order_shipping_method' )
		     ->set_label( __( 'Shipping Method', 'woocommerce' ) );
	}

	public function get_raw_value( $order_id ) {
		$order = wc_get_order( $order_id );

		$value = $order->get_shipping_method();

		if ( ! $value ) {
			return null;
		}

		return $value;
	}

	public function filtering() {
		return new Filtering\ShopOrder\ShippingMethod( $this );
	}

	public function sorting() {
		return new WC\Sorting\ShopOrder\ShippingMethod();
	}

	public function search() {
		return new WC\Search\ShopOrder\ShippingMethod();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}