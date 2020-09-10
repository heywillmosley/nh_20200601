<?php

namespace ACA\WC\Editing\ProductSubscription;

use AC;
use ACA\WC;
use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\StorageModel;
use ACP;
use WC_Product_Subscription;

class Expires extends ACP\Editing\Model\Meta
	implements ACP\Editing\PaginatedOptions {

	public function get_edit_value( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product_Subscription ) {
			return null;
		}

		return parent::get_edit_value( $product_id );
	}

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'clear_button'  => true,
		];
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$period = 'day';
		if ( $id ) {
			$period = get_post_meta( $id, '_subscription_period', true );
		}

		return new AC\Helper\Select\Options\Paginated(
			new WC\Helper\Select\SinglePage(),
			AC\Helper\Select\Options::create_from_array( wcs_get_subscription_ranges( $period ) )
		);
	}

}