<?php

namespace ACA\WC\Search\ShopOrder;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class CouponsUsed extends Comparison\Meta {

	public function __construct() {
		$operators = new Operators(
			[
				Operators::IS_EMPTY,
				Operators::NOT_IS_EMPTY,
			]
		);

		parent::__construct( $operators, '_recorded_coupon_usage_counts', MetaType::POST );
	}

}