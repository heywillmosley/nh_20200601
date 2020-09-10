<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use WC_DateTime;

/**
 * @since 3.3
 */
class Notes extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-wc_order_notes' )
		     ->set_group( 'woocommerce' )
		     ->set_label( __( 'Order Notes', 'woocommerce' ) );
	}

	public function get_value( $id ) {
		$notes = wc_get_order_notes( [
			'order_id' => $id,
		] );

		$count = count( $notes );
		$icon = ac_helper()->html->rounded( $count );

		$content = [];
		foreach ( $notes as $note ) {
			/** @var WC_DateTime $date */
			$date = $note->date_created;

			$note_content = sprintf( '<small>%s</small><br>%s', $date->format( 'F j, Y - H:i' ), $note->content );
			$content[] = $note_content;
		}

		return ac_helper()->html->tooltip( $icon, implode( '<br><br>', $content ) );
	}

}