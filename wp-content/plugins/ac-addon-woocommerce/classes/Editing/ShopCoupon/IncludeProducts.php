<?php

namespace ACA\WC\Editing\ShopCoupon;

use AC;
use ACA\WC\Helper\Select;
use ACP;
use WC_Coupon;

class IncludeProducts extends ACP\Editing\Model implements ACP\Editing\PaginatedOptions {

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'multiple'      => true,
		];
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		$entities = new Select\Entities\Product( compact( 's', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\ProductTitleAndSKU( $entities )
		);
	}

	public function get_edit_value( $id ) {
		return ac_addon_wc_helper()->get_editable_posts_values( $this->column->get_raw_value( $id ) );
	}

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_product_ids( $value );

		return $coupon->save() > 0;
	}

}