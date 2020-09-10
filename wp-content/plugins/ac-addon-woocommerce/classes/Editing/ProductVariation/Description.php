<?php

namespace ACA\WC\Editing\ProductVariation;

use ACP;
use WC_Product_Variation;

class Description extends ACP\Editing\Model {

	public function get_edit_value( $post_id ) {
		$product = new WC_Product_Variation( $post_id );

		return $product->get_description();
	}

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

	/**
	 * @param int    $id
	 * @param string $description
	 *
	 * @return bool
	 */
	public function save( $id, $description ) {
		$product = new WC_Product_Variation( $id );
		$product->set_description( $description );

		return $product->save() > 0;
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}