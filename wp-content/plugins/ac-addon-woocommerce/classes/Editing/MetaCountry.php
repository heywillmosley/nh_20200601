<?php

namespace ACA\WC\Editing;

use ACP;

class MetaCountry extends ACP\Editing\Model\Meta {

	public function get_view_settings() {
		$options = array_merge( [ '' => __( 'None', 'codepress-admin-columns' ) ], WC()->countries->get_countries() );

		return [
			'type'    => 'select',
			'options' => $options,
		];
	}

}
