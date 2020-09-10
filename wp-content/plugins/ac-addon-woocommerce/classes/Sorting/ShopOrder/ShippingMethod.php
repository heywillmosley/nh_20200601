<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Sorting\AbstractModel;

class ShippingMethod extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	/**
	 * Setup clauses to sort by parent
	 *
	 * @param array $clauses array
	 *
	 * @return array
	 * @since 4.0
	 */
	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= "
			{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_order_items ON {$wpdb->posts}.ID = acsort_order_items.order_id
				AND acsort_order_items.order_item_type = 'shipping'
		";
		$clauses['orderby'] = sprintf( "acsort_order_items.order_item_name %s, {$wpdb->posts}.ID", $this->get_order() );

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}