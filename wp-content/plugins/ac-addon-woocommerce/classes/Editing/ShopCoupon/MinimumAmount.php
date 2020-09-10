<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use WC_Coupon;

class MinimumAmount extends ACP\Editing\Model {

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_minimum_amount( $value );

		return $coupon->save() > 0;
	}

}