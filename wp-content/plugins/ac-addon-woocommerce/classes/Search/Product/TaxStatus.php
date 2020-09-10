<?php

namespace ACA\WC\Search\Product;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class TaxStatus extends Comparison\Meta {

	/** @var array */
	private $statuses;

	public function __construct( $statuses ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->statuses = $statuses;

		parent::__construct( $operators, '_tax_status', MetaType::POST );
	}

}