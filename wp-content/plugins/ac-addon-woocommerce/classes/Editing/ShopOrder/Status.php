<?php

namespace ACA\WC\Editing\ShopOrder;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\ShopOrder\Status $column
 */
class Status extends ACP\Editing\Model {

	public function __construct( Column\ShopOrder\Status $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => $this->column->get_order_status_options(),
		];
	}

	public function get_edit_value( $id ) {
		$status = $this->column->get_raw_value( $id );

		if ( strpos( $status, 'wc-' ) !== 0 ) {
			$status = 'wc-' . $status;
		}

		return $status;
	}

	public function save( $id, $value ) {
		$order = wc_get_order( $id );

		return $order->update_status( $value );
	}
}
