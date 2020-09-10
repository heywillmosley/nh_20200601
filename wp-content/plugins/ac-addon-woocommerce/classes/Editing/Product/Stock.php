<?php

namespace ACA\WC\Editing\Product;

use ACP;
use stdClass;
use WC_Product;

class Stock extends ACP\Editing\Model {

	public function get_view_settings() {
		return [
			'type'                => 'wc_stock',
			'manage_stock'        => $this->is_manage_stock_enabled(),
			'disable_revisioning' => true,
		];
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product->is_type( 'simple' ) ) {
			return null;
		}

		$data = new stdClass();
		$data->type = $product->get_stock_status();
		$data->quantity = $product->get_stock_quantity();

		if ( $product->get_manage_stock() && $this->is_manage_stock_enabled() ) {
			$data->type = 'manage_stock';
		}

		return $data;
	}

	protected function is_manage_stock_enabled() {
		return 'yes' === get_option( 'woocommerce_manage_stock' );
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$type = $value['type'];
		$manage_stock = ( 'manage_stock' === $type );

		$product->set_stock_status( $manage_stock ? '' : $type );

		if ( $this->is_manage_stock_enabled() ) {
			$product->set_manage_stock( $manage_stock );
			$this->set_stock_quantity( $product, $value['replace_type'], $value['quantity'] );
		}

		return $product->save() > 0;
	}

	private function set_stock_quantity( WC_Product $product, $type, $stock ) {
		$original_quantity = $product->get_stock_quantity();

		switch ( $type ) {
			case 'increase':
				$stock = $original_quantity + $stock;
				break;
			case 'decrease':
				$stock = $original_quantity - $stock;
				break;
		}

		if ( $stock < 0 ) {
			$stock = 0;
		}

		$product->set_stock_quantity( $stock );
	}

}