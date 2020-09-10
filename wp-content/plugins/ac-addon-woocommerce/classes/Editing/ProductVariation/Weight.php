<?php

namespace ACA\WC\Editing\ProductVariation;

use ACP;

class Weight extends ACP\Editing\Model {

	public function get_edit_value( $post_id ) {
		$product = wc_get_product( $post_id );

		return $product->get_weight();
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_weight( $value );

		return $product->save() > 0;
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}