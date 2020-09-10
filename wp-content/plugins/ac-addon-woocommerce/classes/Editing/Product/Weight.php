<?php

namespace ACA\WC\Editing\Product;

use ACP;

class Weight extends ACP\Editing\Model {

	public function get_edit_value( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( $product->is_virtual() ) {
			return null;
		}

		return $product->get_weight();
	}

	public function get_view_settings() {
		return [
			'type' => 'float',
		];
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_weight( $value );

		return $product->save() > 0;
	}

}