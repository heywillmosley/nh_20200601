<?php

namespace ACA\WC\Editing\Product;

use AC;
use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\StorageModel;
use ACP;
use WC_Product;
use WP_Error;

class Price extends ACP\Editing\Model {

	/** @var string */
	private $default_type;

	public function __construct( AC\Column $column, $default_type = 'regular' ) {
		$this->default_type = 'regular' === $default_type
			? 'regular'
			: 'sale';

		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type'                => 'wc_price_extended',
			'disable_revisioning' => true,
			'default_type'        => $this->default_type,
		];
	}

	/**
	 * @return array
	 */
	private function excluded_types() {
		return [ 'variable', 'grouped' ];
	}

	/**
	 * @param int $id
	 *
	 * @return null|array
	 */
	public function get_edit_value( $id ) {
		$product = $this->get_editable_product( $id );

		if ( ! $product ) {
			return null;
		}

		return [
			'regular' => $this->get_edit_regular_value( $product ),
			'sale'    => $this->get_edit_sale_value( $product ),
		];
	}

	private function get_edit_regular_value( WC_Product $product ) {
		return [
			'price' => $product->get_regular_price(),
		];
	}

	private function get_edit_sale_value( WC_Product $product ) {
		$from_date = $product->get_date_on_sale_from();
		$to_date = $product->get_date_on_sale_to();

		return [
			'price'         => $product->get_sale_price(),
			'schedule_from' => $from_date ? $from_date->format( 'Y-m-d' ) : '',
			'schedule_to'   => $to_date ? $to_date->format( 'Y-m-d' ) : '',
		];
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Product|false
	 */
	private function get_editable_product( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product ) {
			return false;
		}

		if ( $product->is_type( $this->excluded_types() ) ) {
			return false;
		}

		return $product;
	}

	/**
	 * @param int   $id
	 * @param array $value
	 */
	public function save( $id, $value ) {
		switch ( $value['type'] ) {
			case 'regular':
				$model = new StorageModel\Product\Price( wc_get_product( $id ), new EditValue\Product\Price( $value ) );

				break;
			case 'sale':
				$model = new StorageModel\Product\SalePrice( wc_get_product( $id ), new EditValue\Product\SalePrice( $value ) );

				break;
			default:
				return false;
		}

		$result = $model->save();

		if ( $result instanceof WP_Error ) {
			$this->set_error( $result );

			return false;
		}

		return true;
	}

}