<?php

namespace ACA\WC\Editing\Product;

use ACP;

class LowStockThreshold extends ACP\Editing\Model\Meta {

	public function get_view_settings() {
		return [
			'type'        => 'number',
			'placeholder' => __( 'Low stock threshold', 'woocommerce' ),
		];
	}

}