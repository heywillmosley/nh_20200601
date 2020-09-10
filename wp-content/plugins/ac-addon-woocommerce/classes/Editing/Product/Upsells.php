<?php

namespace ACA\WC\Editing\Product;

use AC;
use ACA\WC\Helper\Select;
use ACP;

class Upsells extends ACP\Editing\Model
	implements ACP\Editing\PaginatedOptions {

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
		return ac_addon_wc_helper()->get_editable_posts_values( wc_get_product( $id )->get_upsell_ids() );
	}

	public function save( $id, $ids ) {
		$product = wc_get_product( $id );
		$product->set_upsell_ids( (array) $ids );

		return $product->save() > 0;
	}

}