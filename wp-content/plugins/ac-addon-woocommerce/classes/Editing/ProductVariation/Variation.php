<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Column;
use ACP;
use stdClass;
use WC_Product;
use WC_Product_Attribute;
use WC_Product_Variation;
use WP_Term;

/**
 * @property Column\ProductVariation\Variation $column
 */
class Variation extends ACP\Editing\Model {

	public function __construct( Column\ProductVariation\Variation $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type'          => 'wc_variation',
			'bulk_editable' => false,
		];
	}

	/**
	 * @param int $id
	 *
	 * @return stdClass
	 */
	public function get_edit_value( $id ) {
		$variation = new WC_Product_Variation( $id );
		$product = wc_get_product( $variation->get_parent_id() );

		return (object) [
			'value'   => $variation->get_attributes(),
			'options' => $this->get_product_variation_options( $product ),
		];
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	private function get_product_variation_options( WC_Product $product ) {
		$results = [];

		foreach ( $product->get_attributes() as $key => $attribute ) {
			if ( ! $attribute instanceof WC_Product_Attribute ) {
				continue;
			}

			// Is used for variations
			if ( ! $attribute->get_variation() ) {
				continue;
			}

			$options = [];

			if ( $attribute->is_taxonomy() ) {
				foreach ( $attribute->get_terms() as $term ) {
					if ( $term instanceof WP_Term ) {
						$options[ $term->slug ] = $term->name;
					}
				}
			} else {
				$options = array_combine( $attribute->get_options(), $attribute->get_options() );
			}

			$results[ $key ] = [
				'label'   => $this->column->get_setting_variation()->get_attribute_label( $attribute ),
				'options' => $options,
			];
		}

		return $results;
	}

	public function save( $id, $value ) {
		$variation = new WC_Product_Variation( $id );
		$variation->set_attributes( $value );

		return $variation->save() > 0;
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->get_setting( 'edit' )->set_default( 'on' );
	}

}