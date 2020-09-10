<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Export;
use ACP;

/**
 * @since 2.0
 */
class Orders extends AC\Column
	implements AC\Column\AjaxValue, ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'column-wc-coupon_orders' )
		     ->set_label( __( 'Orders', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order_ids = $this->get_raw_value( $id );

		if ( ! $order_ids ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%s item', '%s items', count( $order_ids ) ), count( $order_ids ) );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $count, $this->get_name() );
	}

	public function get_raw_value( $id ) {
		return ac_addon_wc_helper()->get_order_ids_by_coupon_id( $id );
	}

	public function get_ajax_value( $id ) {
		$values = [];
		foreach ( ac_addon_wc_helper()->get_order_ids_by_coupon_id( $id ) as $order_id ) {
			$values[] = ac_helper()->html->link( get_edit_post_link( $order_id ), $order_id );
		}

		return implode( ', ', $values );
	}

	public function export() {
		return new Export\ShopCoupon\Orders( $this );
	}

}