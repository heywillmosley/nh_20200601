<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\Product\TaxClass $column
 */
class TaxClass extends ACP\Editing\Model {

	public function __construct( Column\Product\TaxClass $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		$options = [ '' => __( 'Standard', 'codepress-admin-columns' ) ];
		$options = array_merge( $options, $this->column->get_tax_classes() );

		return [
			'type'    => 'select',
			'options' => $options,
		];
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_tax_class( $value );

		return $product->save() > 0;
	}

}