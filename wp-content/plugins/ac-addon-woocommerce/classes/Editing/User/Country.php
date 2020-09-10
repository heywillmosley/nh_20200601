<?php

namespace ACA\WC\Editing\User;

use ACP;

class Country extends ACP\Editing\Model\Meta {

	public function get_view_settings() {
		return [
			'type'         => 'select2_dropdown',
			'options'      => WC()->countries->get_countries(),
			'clear_button' => true,
		];
	}

}
