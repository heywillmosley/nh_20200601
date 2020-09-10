<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use WC_Coupon;

class FreeShipping extends ACP\Editing\Model {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'no'  => __( 'No', 'codepress-admin-columns' ),
				'yes' => __( 'Yes', 'codepress-admin-columns' ),
			],
		];
	}

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_free_shipping( 'yes' === $value );

		return $coupon->save() > 0;
	}

}