<?php

namespace ACA\WC\Export\User;

use ACP;

/**
 * @since 2.2.1
 */
class Orders extends ACP\Export\Model {

	public function get_value( $id ) {
		$orders = wc_get_orders( [
			'customer' => $id,
			'status'   => 'any',
			'orderby'  => 'date_completed',
			'order'    => 'ASC',
		] );

		$result = [];
		foreach ( $orders as $order ) {
			$result[] = $order->get_id();
		}

		return implode( ',', $result );
	}

}