<?php

namespace ACA\WC\Editing\ProductVariation;

use ACP;

class Image extends ACP\Editing\Model\Post\FeaturedImage {

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}