<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Column;
use ACP;
use WC_Product_Attribute;
use WP_Error;

/**
 * @property Column\Product\Attributes $column
 */
abstract class Attributes extends ACP\Editing\Model {

	/**
	 * @return false|WC_Product_Attribute
	 */
	abstract protected function create_attribute();

	public function __construct( Column\Product\Attributes $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type' => 'multi_input',
		];
	}

	public function get_edit_value( $id ) {
		$attribute = $this->get_attribute_object( $id );

		if ( ! $attribute ) {
			return [];
		}

		return array_values( $attribute->get_options() );
	}

	/**
	 * @param int $id
	 *
	 * @return false|WC_Product_Attribute
	 */
	protected function get_attribute_object( $id ) {
		$attributes = wc_get_product( $id )->get_attributes();

		if ( ! isset( $attributes[ $this->column->get_attribute() ] ) ) {
			return false;
		}

		return $attributes[ $this->column->get_attribute() ];
	}

	/**
	 * @param int          $id
	 * @param array|string $options
	 *
	 * @return bool
	 */
	public function save( $id, $options ) {
		$attribute = $this->get_attribute_object( $id );

		if ( ! $attribute ) {
			$attribute = $this->create_attribute();
		}

		if ( ! $attribute ) {
			$this->set_error( new WP_Error( 'non-existing-attribute', __( 'Non existing attribute.', 'codepress-admin-columns' ) ) );

			return false;
		}

		$attribute->set_options( $options );

		$product = wc_get_product( $id );

		$attributes = $product->get_attributes();
		$attributes[] = $attribute;

		$product->set_attributes( $attributes );

		return $product->save() > 0;
	}

}