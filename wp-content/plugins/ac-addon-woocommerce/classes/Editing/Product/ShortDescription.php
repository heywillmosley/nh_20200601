<?php

namespace ACA\WC\Editing\Product;

use ACP;

/**
 * @since 3.0
 */
class ShortDescription extends ACP\Editing\Model\Post\Excerpt {

	public function get_view_settings() {
		/* @var ACP\Editing\Settings\Content $setting */
		$setting = $this->column->get_setting( 'edit' );

		return [
			'bulk_editable' => false,
			'type'          => $setting->get_editable_type(),
		];
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->add_setting( new ACP\Editing\Settings\Content( $this->column ) );
	}
}