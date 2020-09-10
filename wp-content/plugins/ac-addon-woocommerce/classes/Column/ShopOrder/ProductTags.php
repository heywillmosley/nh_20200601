<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class ProductTags extends AC\Column
	implements ACP\Export\Exportable, ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-product_tags' );
		$this->set_label( __( 'Product Tags', 'codepress-admin-columns' ) );
	}

	public function get_value( $order_id ) {
		$terms = ac_helper()->taxonomy->get_term_links( $this->get_raw_value( $order_id ), 'product' );

		if ( empty( $terms ) ) {
			return $this->get_empty_char();
		}

		return ac_helper()->string->enumeration_list( $terms, 'and' );

	}

	public function get_taxonomy() {
		return 'product_tag';
	}

	public function get_raw_value( $order_id ) {
		$result = [];
		$product_ids = ac_addon_wc_helper()->get_product_ids_by_order( $order_id );

		foreach ( $product_ids as $product_id ) {
			$terms = get_the_terms( $product_id, $this->get_taxonomy() );

			if ( ! $terms || is_wp_error( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term;
			}
		}

		return $result;
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function filtering() {
		return new Filtering\ShopOrder\ProductTaxonomy( $this, $this->get_taxonomy() );
	}

	public function search() {
		return new Search\ShopOrder\ProductTags();
	}

}