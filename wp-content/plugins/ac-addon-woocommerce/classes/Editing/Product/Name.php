<?php

namespace ACA\WC\Editing\Product;

use ACP;

class Name extends ACP\Editing\Model\Post {

	public function get_view_settings() {
		return [
			'type'         => 'text',
			'display_ajax' => false,
		];
	}

	public function get_edit_value( $id ) {
		return ac_helper()->post->get_raw_field( 'post_title', $id );
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_title' => $value ] );
	}

}