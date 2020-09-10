<?php

namespace ACA\WC\Editing\Product\Attributes;

use ACA\WC\Editing\Product\Attributes;
use WC_Product_Attribute;

class Custom extends Attributes {

	protected function create_attribute() {
		$labels = $this->column->get_setting_attribute()->get_attributes_custom_labels();

		if ( ! isset( $labels[ $this->column->get_attribute() ] ) ) {
			return false;
		}

		$label = $labels[ $this->column->get_attribute() ];

		$attribute = new WC_Product_Attribute();
		$attribute->set_name( $label );

		return $attribute;
	}

}