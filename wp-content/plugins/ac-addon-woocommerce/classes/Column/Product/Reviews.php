<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 3.0
 */
class Reviews extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-product_reviews' )
		     ->set_label( __( 'Reviews', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$count = $this->get_raw_value( $id );

		if ( ! $count ) {
			return $this->get_empty_char();
		}

		$link = add_query_arg( [ 'p' => $id, 'status' => 'approved' ], get_admin_url( null, 'edit-comments.php' ) );

		return ac_helper()->html->link( $link, $count );
	}

	public function get_raw_value( $post_id ) {
		$product = wc_get_product( $post_id );

		return $product->get_review_count();
	}

	public function get_meta_key() {
		return '_wc_review_count';
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Number( $this->get_meta_key(), AC\MetaType::POST );
	}

}