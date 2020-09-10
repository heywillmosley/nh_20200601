<?php

namespace ACA\WC\Editing\Product;

use ACP;

class BackordersAllowed extends ACP\Editing\Model {

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		// Only items that have manage stock enabled can have back orders
		if ( ! $product->managing_stock() ) {
			return null;
		}

		return $product->get_backorders();
	}

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => $this->get_backorder_options(),
		];
	}

	/**
	 * @param int    $id
	 * @param string $value
	 *
	 * @return bool
	 */
	public function save( $id, $value ) {
		if ( ! array_key_exists( $value, $this->get_backorder_options() ) ) {
			return false;
		}

		$product = wc_get_product( $id );
		$product->set_backorders( $value );

		return $product->save() > 0;
	}

	private function get_backorder_options() {
		return [
			'no'     => __( 'Do not allow', 'woocommerce' ),
			'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
			'yes'    => __( 'Allow', 'woocommerce' ),
		];
	}

}