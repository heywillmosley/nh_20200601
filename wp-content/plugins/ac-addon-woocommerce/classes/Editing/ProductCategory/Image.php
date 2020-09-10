<?php

namespace ACA\WC\Editing\ProductCategory;

use ACP;

class Image extends ACP\Editing\Model\Meta {

	public function get_view_settings() {
		$data['type'] = 'media';
		$data['attachment']['library']['type'] = 'image';

		return $data;
	}

}