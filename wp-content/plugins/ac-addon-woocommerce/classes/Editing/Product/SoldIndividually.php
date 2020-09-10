<?php

namespace ACA\WC\Editing\Product;

use ACP;

/**
 * @since 3.0
 */
class SoldIndividually extends ACP\Editing\Model {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'1' => __( 'Sold Individually', 'codepress-admin-columns' ),
				''  => __( 'Not Sold Individually', 'codepress-admin-columns' ),
			],
		];
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_sold_individually( $value );

		return $product->save() > 0;
	}

}