<?php

namespace ACA\WC\Column\User;

use AC\Column;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP\Sorting\Sortable;

/**
 * @since 3.0
 */
class FirstOrder extends Column implements Sortable {

	public function __construct() {
		$this->set_type( 'column-wc-user-first_order' )
		     ->set_group( 'woocommerce' )
		     ->set_label( __( 'First Order', 'codepress-admin-columns' ) );
	}

	protected function get_first_order( $user_id ) {
		$orders = wc_get_orders( [
			'customer' => $user_id,
			'limit'    => 1,
			'status'   => 'completed',
			'orderby'  => 'date_completed',
			'order'    => 'ASC',
		] );

		if ( ! $orders ) {
			return null;
		}

		return $orders[0];
	}

	public function get_value( $user_id ) {
		$order = $this->get_first_order( $user_id );

		if ( ! $order ) {
			return $this->get_empty_char();
		}

		return $this->get_setting( Settings\User\Order::NAME )->format( $order, $order );
	}

	public function get_raw_value( $id ) {
		return $this->get_first_order( $id );
	}

	public function register_settings() {
		$this->add_setting( new Settings\User\Order( $this ) );
	}

	public function sorting() {
		return new Sorting\User\FirstOrder();
	}

}