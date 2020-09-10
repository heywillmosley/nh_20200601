<?php

namespace ACA\WC\Editing\Product;

use ACP;
use WC_Data_Exception;
use WP_Error;

class SKU extends ACP\Editing\Model {

	public function get_edit_value( $id ) {
		return wc_get_product( $id )->get_sku();
	}

	/**
	 * @param int    $id
	 * @param string $sku
	 *
	 * @return bool
	 */
	public function save( $id, $sku ) {
		$product = wc_get_product( $id );

		try {
			$product->set_sku( $sku );
		} catch ( WC_Data_Exception $e ) {
			$this->set_error( new WP_Error( $e->getErrorCode(), $e->getMessage() ) );

			return false;
		}

		return $product->save() > 0;
	}

}