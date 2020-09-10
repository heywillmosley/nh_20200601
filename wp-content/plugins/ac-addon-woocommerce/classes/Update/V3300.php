<?php

namespace ACA\WC\Update;

final class V3300 {

	/**
	 * @var string
	 */
	private $key;

	public function __construct() {
		$this->key = 'aca_wc_update_v3300';
	}

	public function is_applied() {
		return get_option( $this->key );
	}

	private function mark_applied() {
		update_option( $this->key, true, 'no' );
	}

	public function run() {
		global $wpdb;

		$sql = "
			SELECT *
			FROM {$wpdb->options}
			WHERE option_name LIKE 'cpac_options_shop_order%'
		";

		$results = $wpdb->get_results( $sql );

		if ( ! is_array( $results ) ) {
			return;
		}

		// Clear default column headings
		delete_option( 'cpac_options_shop_order__default' );

		foreach ( $results as $row ) {
			$options = maybe_unserialize( $row->option_value );
			$update = false;

			if ( ! is_array( $options ) ) {
				continue;
			}

			foreach ( $options as $k => $v ) {
				if ( ! is_array( $v ) || empty( $v['type'] ) ) {
					continue;
				}

				switch ( $v['type'] ) {
					case 'order_title' :
						$options[ $k ]['type'] = 'order_number';
						$update = true;

						break;
					case 'order_actions' :
						$options[ $k ]['type'] = 'wc_actions';
						$update = true;

						break;
					case 'order_notes' :
					case 'customer_message' :
						unset( $options[ $k ] );
						$update = true;

						break;
					case 'order_status' :
						$options[ $k ]['label'] = __( 'Status', 'woocommerce' );
						$update = true;

						break;
				}
			}

			if ( $update ) {
				update_option( $row->option_name, $options );
			}
		}

		$this->mark_applied();
	}

}