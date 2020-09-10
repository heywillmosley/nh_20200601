<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Column;
use ACP;
use WC_Data_Exception;
use WP_Error;

/**
 * @property Column\Product\Visibility $column
 */
class Visibility extends ACP\Editing\Model {

	public function __construct( Column\Product\Visibility $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => wc_get_product_visibility_options(),
		];
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );

		try {
			$product->set_catalog_visibility( $value );
		} catch ( WC_Data_Exception $e ) {
			$this->set_error( new WP_Error( $e->getErrorCode(), $e->getMessage() ) );

			return false;
		}

		return $product->save() > 0;
	}

}