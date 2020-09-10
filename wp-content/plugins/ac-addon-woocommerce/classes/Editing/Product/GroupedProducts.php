<?php

namespace ACA\WC\Editing\Product;

use AC;
use ACA\WC\Helper\Select;
use ACP;

class GroupedProducts extends ACP\Editing\Model\Meta implements ACP\Editing\PaginatedOptions {

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
		$product = wc_get_product( $id );

		if ( 'grouped' !== $product->get_type() ) {
			return null;
		}

		return ac_addon_wc_helper()->get_editable_posts_values( $product->get_children() );
	}

	public function save( $id, $values ) {
		return parent::save( $id, array_map( 'intval', $values ) );
	}

}