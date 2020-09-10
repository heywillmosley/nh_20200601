<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACP;

/**
 * @since 3.0
 */
class Description extends AC\Column\Meta
	implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_type( 'column-wc-product_description' );
		$this->set_label( __( 'Description', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_variation_description';
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\StringLimit( $this ) );
	}

	public function editing() {
		return new Editing\ProductVariation\Description( $this );
	}

}