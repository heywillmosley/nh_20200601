<?php

namespace ACA\WC\Sorting\Product;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\WarningAware;

class Variation extends AbstractModel implements WarningAware {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['fields'] .= ", ac_variation_count.count";
		$clauses['join'] .= "
			{$join_type} JOIN 
			(
				SELECT ac_variation_count.ID, count( * ) as count, post_parent
				FROM {$wpdb->posts} ac_variation_count
				WHERE 
					post_type = 'product_variation'
					AND post_status = 'publish'
				GROUP BY post_parent
			) ac_variation_count 
			ON ac_variation_count.post_parent = {$wpdb->posts}.ID
		";
		$clauses['orderby'] = sprintf( "ac_variation_count.count %s, {$wpdb->posts}.ID", $this->get_order() );
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}