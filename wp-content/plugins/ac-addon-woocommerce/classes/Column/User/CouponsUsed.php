<?php

namespace ACA\WC\Column\User;

use AC;

/**
 * @since 1.3
 */
class CouponsUsed extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-wc-user_coupons_used' );
		$this->set_label( __( 'Coupons Used', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $user_id ) {
		$coupons = [];

		foreach ( ac_addon_wc_helper()->get_orders_by_user( $user_id ) as $order ) {
			foreach ( $order->get_coupon_codes() as $coupon ) {
				$coupons[] = ac_helper()->html->link( get_edit_post_link( $order->get_id() ), $coupon, [ 'tooltip' => 'order: #' . $order->get_id() ] );
			}
		}

		return implode( ' | ', $coupons );
	}

	/**
	 * @param int $user_id
	 *
	 * @return int Count
	 */
	public function get_raw_value( $user_id ) {
		$coupons = [];

		foreach ( ac_addon_wc_helper()->get_orders_by_user( $user_id ) as $order ) {
			foreach ( $order->get_coupon_codes() as $code ) {
				$coupons[] = $code;
			}
		}

		return count( array_unique( $coupons ) );
	}

}