<?php

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\Export;
use ACA\WC\Sorting;
use ACP;
use WC_Order;

/**
 * @since 1.3
 */
class Orders extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'column-wc-user-orders' )
		     ->set_label( __( 'Orders', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $user_id ) {
		$orders = $this->get_raw_value( $user_id );

		if ( ! $orders ) {
			return $this->get_empty_char();
		}

		$values = [];

		foreach ( $orders as $order ) {
			$hrml = sprintf(
				'<div class="order order-%s" %s>%s</div>'
				, esc_attr( $order->get_status() )
				, ac_helper()->html->get_tooltip_attr( $this->get_order_tooltip( $order ) )
				, ac_helper()->html->link( get_edit_post_link( $order->get_id() ), $order->get_order_number() )
			);

			$values[] = $hrml;
		}

		if ( ! $values ) {
			return $this->get_empty_char();
		}

		return ac_helper()->html->more( $values, $this->get_setting( AC\Settings\Column\NumberOfItems::NAME )->get_value(), '' );
	}

	public function get_raw_value( $user_id ) {
		return wc_get_orders( [
			'customer' => $user_id,
			'status'   => 'any',
			'orderby'  => 'date_completed',
			'order'    => 'ASC',
		] );
	}

	public function sorting() {
		return new Sorting\User\OrderCount();
	}

	public function export() {
		return new Export\User\Orders( $this );
	}

	/**
	 * @param $order WC_Order
	 *
	 * @return string
	 */
	private function get_order_tooltip( $order ) {
		$tooltip = [
			wc_get_order_status_name( $order->get_status() ),
		];

		$item_count = $order->get_item_count();

		if ( $item_count ) {
			$tooltip[] = $item_count . ' ' . __( 'items', 'codepress-admin-columns' );
		}

		$total = $order->get_total();

		if ( $total ) {
			$tooltip[] = get_woocommerce_currency_symbol( $order->get_currency() ) . wc_trim_zeros( number_format( $total, 2 ) );
		}

		$tooltip[] = ac_format_date( get_option( 'date_format' ), strtotime( $order->get_date_created() ) );

		return implode( ' | ', $tooltip );
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\NumberOfItems( $this ) );
	}

}