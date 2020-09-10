<?php

namespace ACA\WC\Editing\User;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\User\Address $column
 */
class Address extends ACP\Editing\Model\Meta {

	public function __construct( Column\User\Address $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		$settings = parent::get_view_settings();

		$settings['placeholder'] = $this->column->get_setting_address_property()->get_address_property_label();

		return $settings;
	}

}
