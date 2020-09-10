<?php

namespace ACA\WC\Editing\Product;

use ACP;

class Featured extends ACP\Editing\Model {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'no'  => __( 'No', 'codepress-admin-columns' ),
				'yes' => __( 'Yes', 'codepress-admin-columns' ),
			],
		];
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		return $product->get_featured();
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_featured( $value );

		return $product->save() > 0;
	}

}