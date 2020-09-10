<?php

namespace ACA\WC\ListScreen;

use ACA\WC\Column;
use ACP;

class ProductVariation extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'product_variation' );

		$this->set_group( 'woocommerce' );
	}

	protected function register_column_types() {
		$this->register_column_type( new ACP\Column\Actions );
		$this->register_column_type( new ACP\Column\Post\AuthorName );
		$this->register_column_type( new ACP\Column\Post\DatePublished );
		$this->register_column_type( new ACP\Column\Post\ID );
		$this->register_column_type( new ACP\Column\Post\LastModifiedAuthor );
		$this->register_column_type( new ACP\Column\Post\Slug );
		$this->register_column_type( new ACP\Column\Post\Status );
		$this->register_column_type( new ACP\Column\CustomField );

		$this->register_column_type( new Column\Product\ShippingClass );

		$this->register_column_types_from_dir( 'ACA\WC\Column\ProductVariation' );
	}

}