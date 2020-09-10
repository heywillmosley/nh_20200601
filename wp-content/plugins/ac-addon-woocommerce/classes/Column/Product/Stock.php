<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;

/**
 * @since 1.1
 */
class Stock extends AC\Column
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'is_in_stock' )
		     ->set_original( true );
	}

	public function editing() {
		return new Editing\Product\Stock( $this );
	}

	public function export() {
		return new Export\Product\Stock( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Number( '_stock', AC\MetaType::POST );
	}

}