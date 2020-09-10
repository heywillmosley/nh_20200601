<?php

namespace ACA\WC\Editing\ProductVariation;

use ACP;
use WC_Product_Variation;

class Enabled extends ACP\Editing\Model {

	public function get_view_settings() {

		return [
			'type'    => 'togglable',
			'options' => [
				'private' => __( 'Private', 'codepress-admin-columns' ),
				'publish' => __( 'Published', 'codepress-admin-columns' ),
			],
		];
	}

	public function get_edit_value( $id ) {
		$variation = new WC_Product_Variation( $id );

		return $variation->get_status();
	}

	public function save( $id, $value ) {
		$variation = new WC_Product_Variation( $id );
		$variation->set_status( $value );

		return $variation->save() > 0;
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}