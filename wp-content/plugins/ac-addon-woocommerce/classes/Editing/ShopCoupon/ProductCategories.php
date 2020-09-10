<?php

namespace ACA\WC\Editing\ShopCoupon;

use AC;
use ACA\WC\Column;
use ACP;
use ACP\Helper\Select;
use WC_Coupon;

/**
 * @property Column\ShopCoupon\ProductsCategories $column
 */
class ProductCategories extends ACP\Editing\Model
	implements ACP\Editing\PaginatedOptions {

	public function __construct( Column\CouponProductCategories $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'multiple'      => true,
		];
	}

	private function get_product_category() {
		return 'product_cat';
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->get_product_category(),
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);
	}

	public function get_edit_value( $id ) {
		$term_ids = $this->column->get_raw_value( $id );

		if ( empty( $term_ids ) ) {
			return false;
		}

		$values = [];

		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id, $this->get_product_category() );
			if ( ! $term ) {
				continue;
			}

			$values[ $term->term_id ] = htmlspecialchars_decode( $term->name );
		}

		return $values;
	}

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_product_categories( $value );

		return $coupon->save() > 0;
	}

}