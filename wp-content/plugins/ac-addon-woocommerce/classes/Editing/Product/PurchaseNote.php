<?php

namespace ACA\WC\Editing\Product;

use ACP;

/**
 * @since 3.0
 */
class PurchaseNote extends ACP\Editing\Model\Meta {

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

}