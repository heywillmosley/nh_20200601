<?php

namespace ACA\WC\Editing\ShopCoupon;

use WC_Coupon;

class ExcludeProductCategories extends ProductCategories {

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_excluded_product_categories( $value );

		return $coupon->save() > 0;
	}

}