<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;

class Weight extends AC\Column\Meta
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-weight' );
		$this->set_label( __( 'Weight', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_weight';
	}

	public function get_value( $post_id ) {
		$weight = wc_get_product( $post_id )->get_weight();

		if ( ! $weight ) {
			return $this->get_empty_char();
		}

		return wc_format_weight( $weight );

	}

	public function is_valid() {
		return function_exists( 'wc_product_weight_enabled' ) && wc_product_weight_enabled();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function editing() {
		return new Editing\Product\Weight( $this );
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Decimal( $this->get_meta_key(), AC\MetaType::POST );
	}

}