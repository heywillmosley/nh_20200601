<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class TotalSales extends AbstractModel {

	/**
	 * @var array
	 */
	private $status;

	public function __construct( array $status = null ) {
		parent::__construct();

		if ( null === $status ) {
			$status = [ 'wc-completed' ];
		}

		$this->status = $status;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$where = $this->status
			? sprintf( "AND acsort_posts.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->status ) ) )
			: '';

		$order = esc_sql( $this->get_order() );

		$join_type = 'INNER';

		$query->query_fields .= ", SUM( acsort_postmeta2.meta_value ) AS acsort_total";
		$query->query_from .= " 
				{$join_type} JOIN {$wpdb->postmeta} AS acsort_postmeta ON acsort_postmeta.meta_value = {$wpdb->users}.ID 
					AND acsort_postmeta.meta_key = '_customer_user'
				{$join_type} JOIN {$wpdb->postmeta} AS acsort_postmeta2 ON acsort_postmeta.post_id = acsort_postmeta2.post_id
					AND acsort_postmeta2.meta_key = '_order_total'
				{$join_type} JOIN {$wpdb->posts} AS acsort_posts ON acsort_posts.ID = acsort_postmeta.post_id
					AND acsort_posts.post_type = 'shop_order'
					{$where}
		";

		$query->query_orderby = "
					GROUP BY {$wpdb->users}.ID
					ORDER BY acsort_total $order
				";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}