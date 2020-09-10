<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Coupon;

/**
 * @since 1.1
 */
class ExcludeProducts extends AC\Column
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-exclude_products' );
		$this->set_label( __( 'Excluded Products', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $post_id ) {
		$products = [];

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			$products[] = ac_helper()->html->link( get_edit_post_link( $id ), get_the_title( $id ) );
		}

		$value = implode( ', ', array_filter( $products ) );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return (array) $coupon->get_excluded_product_ids();
	}

	public function editing() {
		return new Editing\ShopCoupon\ExcludeProducts( $this );
	}

	public function search() {
		return new Search\ShopCoupon\Products( 'exclude_product_ids' );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}