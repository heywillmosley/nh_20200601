<?php

namespace ACA\WC\Column\ShopCoupon;

use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Coupon;

/**
 * @since 1.1
 */
class MaximumAmount extends ACP\Column\Meta
	implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-maximum_amount' )
		     ->set_label( __( 'Maximum Amount', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return 'maximum_amount';
	}

	public function get_value( $id ) {
		$amount = $this->get_raw_value( $id );

		if ( ! $amount ) {
			return $this->get_empty_char();
		}

		return wc_price( $amount );
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function editing() {
		return new Editing\ShopCoupon\MaximumAmount( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Number( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_maximum_amount();
	}

}