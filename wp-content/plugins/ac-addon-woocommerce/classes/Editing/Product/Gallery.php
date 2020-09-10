<?php

namespace ACA\WC\Editing\Product;

use ACP;

class Gallery extends ACP\Editing\Model {

	public function get_view_settings() {
		$data = [
			'type'         => 'media',
			'clear_button' => true,
			'attachment'   => [
				'library' => [
					'type' => 'image',
				],
			],
			'multiple'     => true,
			'store_values' => true,
		];

		return $data;
	}

	public function get_edit_value( $id ) {
		return wc_get_product( $id )->get_gallery_image_ids();
	}

	/**
	 * @param int   $id
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function save( $id, $ids ) {
		$product = wc_get_product( $id );
		$product->set_gallery_image_ids( $ids );

		return $product->save() > 0;
	}

}