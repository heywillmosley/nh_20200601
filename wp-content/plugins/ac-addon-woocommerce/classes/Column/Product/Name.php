<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;

/**
 * @since 1.2
 */
class Name extends AC\Column
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'name' );
		$this->set_original( true );
	}

	public function editing() {
		return new Editing\Product\Name( $this );
	}

	public function export() {
		return new ACP\Export\Model\Post\Title( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Title();
	}

}