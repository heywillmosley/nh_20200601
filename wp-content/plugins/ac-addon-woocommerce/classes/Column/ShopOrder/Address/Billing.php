<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace ACA\WC\Column\ShopOrder\Address;

use ACA\WC\Column\ShopOrder\Address;
use ACA\WC\Settings;

/**
 * @since 3.0
 */
class Billing extends Address {

	public function __construct() {
		$this->set_type( 'column-wc-order_billing_address' );
		$this->set_label( __( 'Billing Address', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		if ( ! $this->get_address_property() ) {
			return false;
		}

		return '_billing_' . $this->get_address_property();
	}

	protected function get_formatted_address( \WC_Order $order ) {
		return $order->get_formatted_billing_address();
	}

	public function get_setting_address_object() {
		return new Settings\Address\Billing( $this );
	}

}