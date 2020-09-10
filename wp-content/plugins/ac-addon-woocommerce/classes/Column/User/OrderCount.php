<?php

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.3
 */
class OrderCount extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-user-order_count' );
		$this->set_label( __( 'Number of Orders', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $user_id ) {
		$count = $this->get_raw_value( $user_id );

		$link = add_query_arg( [
			'post_type'      => 'shop_order',
			'_customer_user' => $user_id,
		], admin_url( 'edit.php' ) );

		return sprintf( '<a href="%s">%s</a>', $link, $count );
	}

	public function get_raw_value( $user_id ) {
		return count( ac_addon_wc_helper()->get_order_ids_by_user( $user_id, 'any' ) );
	}

	public function sorting() {
		return new Sorting\User\OrderCount();
	}

	public function search() {
		return new Search\User\OrderCount();
	}

}