<?php

namespace ACA\WC\Editing\Product;

use AC;
use ACA\WC\Helper\Select;
use ACP;
use ACP\Editing\PaginatedOptions;

class Crosssells extends ACP\Editing\Model
	implements PaginatedOptions {

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
		return ac_addon_wc_helper()->get_editable_posts_values( wc_get_product( $id )->get_cross_sell_ids() );
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_cross_sell_ids( $value );

		return $product->save() > 0;
	}

}