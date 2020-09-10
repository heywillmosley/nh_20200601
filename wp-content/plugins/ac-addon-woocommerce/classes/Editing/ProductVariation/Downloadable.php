<?php

namespace ACA\WC\Editing\ProductVariation;

use ACP;
use WC_Product_Variation;

class Downloadable extends ACP\Editing\Model {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'yes' => __( 'Yes', 'codepress-admin-columns' ),
				'no'  => __( 'No', 'codepress-admin-columns' ),
			],
		];
	}

	public function get_edit_value( $id ) {
		$variation = new WC_Product_Variation( $id );

		return $variation->get_downloadable() ? 'yes' : 'no';
	}

	public function save( $id, $value ) {
		$variation = new WC_Product_Variation( $id );
		$variation->set_downloadable( $value );

		return $variation->save() > 0;
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}
