<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACP;
use WC_Coupon;

/**
 * @since 1.0
 */
class CouponCode extends AC\Column
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'coupon_code' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_code();
	}

	public function editing() {
		return new Editing\ShopCoupon\CouponCode( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Title();
	}

}