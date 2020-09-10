<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Settings;
use ACP;
use WC_Order;

/**
 * @since 3.0
 */
abstract class Address extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	/**
	 * @param WC_Order $order
	 *
	 * @return string
	 */
	abstract protected function get_formatted_address( WC_Order $order );

	/**
	 * @return Settings\Address
	 */
	abstract protected function get_setting_address_object();

	/**
	 * @return false|string
	 */
	protected function get_address_property() {
		$setting = $this->get_setting( 'address_property' );

		if ( ! $setting instanceof Settings\Address ) {
			return false;
		}

		return $setting->get_address_property();
	}

	public function get_raw_value( $id ) {
		if ( ! $this->get_meta_key() ) {
			return $this->get_formatted_address( wc_get_order( $id ) );
		}

		return parent::get_raw_value( $id );
	}

	public function register_settings() {
		$this->add_setting( $this->get_setting_address_object() );
	}

	public function filtering() {
		if ( ! $this->get_meta_key() ) {
			return new ACP\Filtering\Model\Disabled( $this );
		}

		return new ACP\Filtering\Model\Meta( $this );
	}

	public function search() {
		if ( ! $this->get_meta_key() ) {
			return false;
		}

		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), AC\MetaType::POST );
	}

	public function sorting() {
		if ( ! $this->get_meta_key() ) {
			return new ACP\Sorting\Model\Disabled();
		}

		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		switch ( $this->get_address_property() ) {
			case '' :
				return new ACP\Editing\Model\Disabled( $this );

			case 'country' :
				return new Editing\MetaCountry( $this );

			default :
				return new ACP\Editing\Model\Meta( $this );
		}
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}