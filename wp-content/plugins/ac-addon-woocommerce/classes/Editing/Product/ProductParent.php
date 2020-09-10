<?php

namespace ACA\WC\Editing\Product;

use AC;
use ACA\WC\Helper\Select;
use ACP;

// todo: not used atm

class ProductParent extends ACP\Editing\Model\Post
	implements ACP\Editing\PaginatedOptions {

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'clear_button'  => true,
		];
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		$entities = new Select\Entities\Product( [
			's'            => $s,
			'paged'        => $paged,
			'post__not_in' => $id,
			'tax_query'    => [
				[
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => [ 'grouped', 'variable' ],
					'operator' => 'NOT IN',
				],
			],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\ProductTitleAndSKU( $entities )
		);
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->is_type( [ 'variable', 'grouped' ] ) ) {
			return null;
		}

		return ac_addon_wc_helper()->get_editable_posts_values( $product->get_parent_id() );
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_parent' => $value ] );
	}

}