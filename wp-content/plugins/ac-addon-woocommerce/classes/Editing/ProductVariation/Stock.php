<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Editing;
use stdClass;

class Stock extends Editing\Product\Stock {

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		$data = new stdClass();
		$data->type = $product->get_stock_status();
		$data->quantity = $product->get_stock_quantity();

		if ( $product->get_manage_stock() && $this->is_manage_stock_enabled() ) {
			$data->type = 'manage_stock';
		}

		return $data;
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}