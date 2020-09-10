<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class PurchaseNote extends AC\Column\Meta
	implements ACP\Export\Exportable, ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-product_purchase_note' );
		$this->set_label( __( 'Purchase Note', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_purchase_note';
	}

	public function export() {
		return new ACP\Export\Model\StrippedRawValue( $this );
	}

	public function editing() {
		return new Editing\Product\PurchaseNote( $this );
	}

	public function filtering() {
		return new Filtering\Product\PurchaseNote( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), AC\MetaType::POST );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Product\UseIcon( $this ) );
	}

}