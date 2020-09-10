<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;

class CouponCode extends ACP\Editing\Model\Post {

	public function get_view_settings() {
		return [
			'type' => 'text',
			'js'   => [
				'selector' => 'strong > a',
			],
		];
	}

	/**
	 * @param int    $id
	 * @param string $title
	 *
	 * @return bool
	 */
	public function save( $id, $title ) {
		return $this->update_post( $id, [ 'post_title' => $title ] );
	}

}