<?php

namespace ACA\WC\Plugin\Update;

use AC;

class V30 extends AC\Plugin\Update {

	public function apply_update() {
		$this->update_columns();
	}

	protected function set_version() {
		$this->version = '3.0';
	}

	/**
	 * Change the roles columns to the author column
	 */
	private function update_columns() {
		global $wpdb;

		$sql = "
			SELECT *
			FROM {$wpdb->options}
			WHERE option_name LIKE 'cpac_options_%'
		";

		$results = $wpdb->get_results( $sql );

		if ( ! is_array( $results ) ) {
			return;
		}

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
					case 'column-wc-order-productmeta':
						$options[ $k ] = $this->update_to_product_column( $v, 'custom_field' );
						$update = true;

						break;
					case 'column-wc-product_thumbnails':
						$options[ $k ] = $this->update_to_product_column( $v, 'thumbnail' );
						$update = true;

						break;
					case 'column-wc-order_customer_role':
						$options[ $k ] = $this->update_to_customer_column( $v, 'roles' );
						$update = true;

						break;
					case 'column-wc-order-usermeta':
						$options[ $k ] = $this->update_to_customer_column( $v, 'custom_field' );
						$update = true;

						break;
				}

			}

			if ( $update ) {
				update_option( $row->option_name, $options );
			}
		}
	}

	/**
	 * @param array  $column
	 * @param string $property_display
	 *
	 * @return array
	 */
	private function update_to_product_column( $column, $property_display = 'title' ) {
		$column['type'] = 'column-wc-product';
		$column['post_property_display'] = $property_display;

		return $column;
	}

	/**
	 * @param array  $column
	 * @param string $property_display
	 *
	 * @return array
	 */
	private function update_to_customer_column( $column, $property_display = 'billing_address' ) {
		$column['type'] = 'column-wc-order_customer';
		$column['customer_property_display'] = $property_display;

		return $column;
	}

}