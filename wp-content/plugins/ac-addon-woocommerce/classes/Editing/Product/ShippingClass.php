<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\Product\ShippingClass $column
 */
class ShippingClass extends ACP\Editing\Model\Post\Taxonomy {

	public function __construct( Column\Product\ShippingClass $column ) {
		parent::__construct( $column );
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product || ! $product->needs_shipping() ) {
			return null;
		}

		return parent::get_edit_value( $id );
	}

	public function get_view_settings() {
		$settings = parent::get_view_settings();

		$settings['type'] = 'select';
		$settings['options'] = [ '' => __( 'No shipping class', 'codepress-admin-columns' ) ] + $this->get_term_options();

		return $settings;
	}

	public function register_settings() {
		$this->column->add_setting( new ACP\Editing\Settings( $this->column ) )
		             ->add_setting( new ACP\Editing\Settings\BulkEditing( $this->column ) );
	}

}