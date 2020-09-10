<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use WC_Coupon;

class Amount extends ACP\Editing\Model {

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_amount( $value );

		return $coupon->save() > 0;
	}

}