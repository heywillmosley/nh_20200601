<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Column;
use ACP;
use WC_Data_Exception;
use WP_Error;

/**
 * @property Column\Product\TaxStatus $column
 */
class TaxStatus extends ACP\Editing\Model {

	public function __construct( Column\Product\TaxStatus $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		$settings = [
			'type'    => 'select',
			'options' => $this->column->get_tax_status(),
		];

		return $settings;
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );

		try {
			$product->set_tax_status( $value );
		} catch ( WC_Data_Exception $e ) {
			$this->set_error( new WP_Error( $e->getErrorCode(), $e->getMessage() ) );

			return false;
		}

		return $product->save() > 0;
	}

}