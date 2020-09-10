<?php

namespace ACA\WC\Editing\ProductSubscription;

use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\StorageModel;
use ACP;
use WC_Product_Subscription;

class Period extends ACP\Editing\Model {

	const KEY_INTERVAL = '_subscription_period_interval';
	const KEY_PERIOD = '_subscription_period';

	public function get_edit_value( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product_Subscription ) {
			return null;
		}

		return [
			'interval' => $product->get_meta( self::KEY_INTERVAL ),
			'period'   => $product->get_meta( self::KEY_PERIOD ),
		];
	}

	public function get_view_settings() {
		return [
			'type'             => 'wc_subscription_period',
			'interval_options' => wcs_get_subscription_period_interval_strings(),
			'period_options'   => wcs_get_subscription_period_strings(),
		];
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );

		if ( ! $product instanceof WC_Product_Subscription ) {
			return false;
		}

		update_post_meta( $id, self::KEY_INTERVAL, isset( $value['interval'] ) ? $value['interval'] : '' );
		update_post_meta( $id, self::KEY_PERIOD, isset( $value['period'] ) ? $value['period'] : '' );

		return true;
	}

}