<?php

namespace ACA\WC\Editing\Product;

use ACP;

class Dimensions extends ACP\Editing\Model {

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->is_virtual() ) {
			return null;
		}

		return (object) parent::get_edit_value( $id );
	}

	public function get_view_settings() {
		return [
			'type' => 'dimensions',
		];
	}

	public function save( $id, $value ) {
		if ( ! is_array( $value ) || ( ! isset( $value['length'], $value['width'], $value['height'] ) ) ) {
			return false;
		}

		$product = wc_get_product( $id );

		if ( $product->is_virtual() ) {
			return false;
		}

		$product->set_length( $value['length'] );
		$product->set_width( $value['width'] );
		$product->set_height( $value['height'] );

		return $product->save() > 0;
	}

}