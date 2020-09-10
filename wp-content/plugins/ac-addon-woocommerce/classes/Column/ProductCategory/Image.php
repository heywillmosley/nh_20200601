<?php

namespace ACA\WC\Column\ProductCategory;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;

class Image extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'thumb' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_meta_key() {
		return 'thumbnail_id';
	}

	public function editing() {
		return new Editing\ProductCategory\Image( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Image( $this->get_meta_key(), $this->get_meta_type() );
	}

}