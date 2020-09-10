<?php

namespace ACA\WC\Field\ShopSubscription\SubscriptionDate;

use ACA\WC\Editing;
use ACA\WC\Field\ShopSubscription\SubscriptionDate;
use ACA\WC\Search;
use WC_Subscription;

class TrialEnd extends SubscriptionDate {

	public function set_label() {
		$this->label = __( 'Trial End', 'woocommerce-subscriptions' );
	}

	public function get_date( WC_Subscription $subscription ) {
		return get_post_meta( $subscription->get_id(), $this->get_meta_key() );
	}

	public function get_meta_key() {
		return '_schedule_trial_end';
	}

	public function editing() {
		return new Editing\ShopSubscription\Date( $this->column, 'trial_end' );
	}

}