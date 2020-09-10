<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Editing;

class SKU extends Editing\Product\SKU {

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}