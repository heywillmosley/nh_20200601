<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use WC_Coupon;

class Type extends ACP\Editing\Model {

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => wc_get_coupon_types(),
		];
	}

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_discount_type( $value );

		return $coupon->save() > 0;
	}

}