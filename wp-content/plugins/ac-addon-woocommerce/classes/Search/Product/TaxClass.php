<?php

namespace ACA\WC\Search\Product;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class TaxClass extends Comparison\Meta {

	/** @var array */
	private $tax_classes;

	public function __construct( $tax_classes ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->tax_classes = $tax_classes;

		parent::__construct( $operators, '_tax_class', MetaType::POST );
	}

}