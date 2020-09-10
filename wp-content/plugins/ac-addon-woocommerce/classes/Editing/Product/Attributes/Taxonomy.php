<?php

namespace ACA\WC\Editing\Product\Attributes;

use ACP;
use WC_Product;
use WC_Product_Attribute;

class Taxonomy extends ACP\Editing\Model\Post\Taxonomy {

	public function save( $id, $value ) {
		$this->maybe_attach_taxonomy_attribute( wc_get_product( $id ), $this->column->get_taxonomy() );

		parent::save( $id, $value );
	}

	/**
	 * Attach attribute to product only if was not attached.
	 *
	 * @param WC_Product $product
	 * @param string     $taxonomy_name
	 */
	private function maybe_attach_taxonomy_attribute( WC_Product $product, $taxonomy_name ) {
		$atts = $product->get_attributes();

		if ( array_key_exists( $taxonomy_name, $atts ) ) {
			return;
		}

		$product_attribute = new WC_Product_Attribute();

		$product_attribute->set_id( wc_attribute_taxonomy_id_by_name( $taxonomy_name ) );
		$product_attribute->set_name( $taxonomy_name );

		$atts[] = $product_attribute;

		$product->set_attributes( $atts );

		$product->save();
	}

}