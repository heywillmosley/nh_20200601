<?php

namespace ACA\WC\Editing\Product;

use AC;
use ACP;
use WC_Cache_Helper;

class Type extends ACP\Editing\Model {

	/**
	 * @var array
	 */
	private $simple_product_types;

	public function __construct( AC\Column $column, $simple_product_types ) {
		parent::__construct( $column );

		$this->simple_product_types = $simple_product_types;
	}

	public function get_edit_value( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( in_array( $product->get_type(), $this->get_unchangeable_types() ) ) {
			return null;
		}

		return [
			'type'         => $product->get_type(),
			'virtual'      => $product->is_virtual(),
			'downloadable' => $product->is_downloadable(),
		];
	}

	private function get_unchangeable_types() {
		return [ 'subscription', 'variable_subscription' ];
	}

	public function get_view_settings() {
		return [
			'type'         => 'wc_product_type',
			'options'      => wc_get_product_types(),
			'simple_types' => $this->simple_product_types,
		];
	}

	public function save( $id, $value ) {
		if ( isset( $value['type'] ) ) {
			wp_set_object_terms( $id, $value['type'], 'product_type' );
		}

		$cache_key = WC_Cache_Helper::get_cache_prefix( 'product_' . $id ) . '_type_' . $id;
		wp_cache_delete( $cache_key, 'products' );
		
		$product = wc_get_product( $id );

		if ( isset( $value['downloadable'] ) ) {
			$product->set_downloadable( $value['downloadable'] );
		}

		if ( isset( $value['virtual'] ) ) {
			$product->set_virtual( $value['virtual'] );
		}

		$product->save();

		return true;
	}

}