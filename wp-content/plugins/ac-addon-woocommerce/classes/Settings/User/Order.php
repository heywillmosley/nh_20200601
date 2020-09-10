<?php

namespace ACA\WC\Settings\User;

use AC;
use AC\View;
use WC_Order;

class Order extends AC\Settings\Column {

	const NAME = 'order';

	const TYPE_DATE = 'date';
	const TYPE_AMOUNT = 'order';

	/**
	 * @var string
	 */
	private $order_display;

	protected function set_name() {
		$this->name = self::NAME;
	}

	protected function define_options() {
		return [
			'order_display' => self::TYPE_DATE,
		];
	}

	public function get_dependent_settings() {
		$setting = [];

		switch ( $this->get_order_display() ) {
			case self::TYPE_DATE :
				$setting[] = new AC\Settings\Column\Date( $this->column );
				break;
		}

		return $setting;
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		return new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	protected function get_display_options() {
		return [
			self::TYPE_DATE   => __( 'Date', 'codepress-admin-columns' ),
			self::TYPE_AMOUNT => __( 'Amount', 'codepress-admin-columns' ),
		];
	}

	/**
	 * @return string
	 */
	public function get_order_display() {
		return $this->order_display;
	}

	/**
	 * @param string $order_display
	 *
	 * @return bool
	 */
	public function set_order_display( $order_display ) {
		$this->order_display = $order_display;

		return true;
	}

	private function render_value( $value, $id ) {
		return sprintf( '%s <small>(%s)</small>', $value, $id );
	}

	private function render_order_number( WC_Order $order ) {
		$order_number = sprintf( '#%s', $order->get_order_number() );

		$edit_link = $order->get_edit_order_url();

		if ( $edit_link ) {
			$order_number = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $order->get_id() ), $order_number );
		}

		return $order_number;
	}

	/**
	 * @param string $value
	 * @param mixed  $original_value
	 *
	 * @return string|int
	 */
	public function format( $order, $original_value ) {

		switch ( $this->get_order_display() ) {
			case self::TYPE_AMOUNT :
				return $this->render_value( $order->get_formatted_order_total(), $this->render_order_number( $order ) );
			case self::TYPE_DATE :
				$formatter = $this->get_column()->get_setting( AC\Settings\Column\Date::NAME );

				return $this->render_value( $formatter->format( $order->get_date_completed()->getTimestamp(), null ), $this->render_order_number( $order ) );
		}

		return $order;
	}

}