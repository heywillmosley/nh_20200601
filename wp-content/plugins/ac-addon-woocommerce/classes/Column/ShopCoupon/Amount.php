<?php

namespace ACA\WC\Column\ShopCoupon;

use AC\MetaType;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Coupon;

/**
 * @since 1.0
 */
class Amount extends ACP\Column\Meta
	implements ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'amount' )
		     ->set_original( true );
	}

	public function get_meta_key() {
		return 'coupon_amount';
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_amount();
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Decimal( $this->get_meta_key(), MetaType::POST );
	}

	public function editing() {
		return new Editing\ShopCoupon\Amount( $this );
	}

	public function export() {
		return new Export\ShopCoupon\Amount( $this );
	}

}