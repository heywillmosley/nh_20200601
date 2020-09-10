<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use WC_Coupon;

class EmailRestrictions extends ACP\Editing\Model {

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_email_restrictions( $value );

		return $coupon->save() > 0;
	}

	public function get_view_settings() {
		return [
			'type' => 'multi_input',
		];
	}

}