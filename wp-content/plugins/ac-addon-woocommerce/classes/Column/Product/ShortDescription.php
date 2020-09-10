<?php

namespace ACA\WC\Column\Product;

namespace ACA\WC\Column\Product;

use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACP;

/**
 * @since 3.0
 */
class ShortDescription extends ACP\Column\Post\Excerpt {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-product_short_description' );
		$this->set_label( __( 'Short Description' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $post_id ) {
		if ( ! has_excerpt( $post_id ) ) {
			return $this->get_empty_char();
		}

		return parent::get_value( $post_id );
	}

	public function editing() {
		return new Editing\Product\ShortDescription( $this );
	}

	public function filtering() {
		return new Filtering\Product\ShortDescription( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_excerpt' );
	}

}