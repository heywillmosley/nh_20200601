<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;

class Description extends ACP\Editing\Model\Post {

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_excerpt' => $value ] );
	}

}