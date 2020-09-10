<?php

namespace ACA\WC\Editing\ShopSubscription;

use ACP;
use Exception;
use WP_Error;

class Date extends ACP\Editing\Model {

	/**
	 * @var string
	 */
	private $date_key;

	public function __construct( $column, $date_key ) {
		parent::__construct( $column );

		$this->date_key = $date_key;
	}

	public function get_view_settings() {
		return [
			'type' => 'date_time',
		];
	}

	public function save( $id, $value ) {
		$subscription = wcs_get_subscription( $id );

		try {
			$subscription->update_dates( [
				$this->date_key => $value,
			], get_option( 'timezone_string' ) );

			$subscription->save();
		} catch ( Exception $exception ) {
			return new WP_Error( 'not-validated', $exception->getMessage() );
		}

		return true;
	}
}
