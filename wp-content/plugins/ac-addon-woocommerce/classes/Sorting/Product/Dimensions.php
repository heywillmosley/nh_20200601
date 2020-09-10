<?php

namespace ACA\WC\Sorting\Product;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\WarningAware;

class Dimensions extends AbstractModel implements WarningAware {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$clauses['fields'] .= ", SUM( acsort_postmeta1.meta_value * acsort_postmeta2.meta_value * acsort_postmeta3.meta_value ) AS acsort_dimensions";
		$clauses['join'] .= "
				INNER JOIN {$wpdb->postmeta} AS acsort_postmeta1
					ON acsort_postmeta1.post_id = {$wpdb->posts}.ID
					AND acsort_postmeta1.meta_key = '_length' 
				INNER JOIN {$wpdb->postmeta} AS acsort_postmeta2
					ON acsort_postmeta2.post_id = {$wpdb->posts}.ID
					AND acsort_postmeta2.meta_key = '_width' 
				INNER JOIN {$wpdb->postmeta} AS acsort_postmeta3
					ON acsort_postmeta3.post_id = {$wpdb->posts}.ID
					AND acsort_postmeta3.meta_key = '_height' 
				";

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( "acsort_dimensions %s, {$wpdb->posts}.ID", $this->get_order() );

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}