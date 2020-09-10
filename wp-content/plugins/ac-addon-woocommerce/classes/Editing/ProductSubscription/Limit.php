<?php

namespace ACA\WC\Editing\ProductSubscription;

use AC;
use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\StorageModel;
use ACP;
use WC_Product_Subscription;

class Limit extends ACP\Editing\Model\Meta {

	/**
	 * @var array
	 */
	private $options;

	public function __construct( AC\Column\Meta $column, $options ) {
		$this->options = $options;

		parent::__construct( $column );
	}

	public function get_edit_value( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product_Subscription ) {
			return null;
		}

		return parent::get_edit_value( $product_id );
	}

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => $this->options,
		];
	}

}