<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class LastOrder extends AbstractModel {

	/**
	 * @var string
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

		$order = esc_sql( $this->get_order() );

		$where_status = $this->status
			? sprintf( " AND acsort_orders.post_status IN ( %s )", $this->esc_sql_array( $this->status ) )
			: '';

		$query->query_fields .= ", MAX( acsort_order_postmeta.meta_value ) AS acsort_lastorder";
		$query->query_from .= " 
					INNER JOIN {$wpdb->postmeta} AS acsort_postmeta 
						ON {$wpdb->users}.ID = acsort_postmeta.meta_value
						AND acsort_postmeta.meta_key = '_customer_user'
					INNER JOIN {$wpdb->posts} AS acsort_orders
						ON acsort_orders.ID = acsort_postmeta.post_id
						AND acsort_orders.post_type = 'shop_order'
						{$where_status}
					INNER JOIN $wpdb->postmeta AS acsort_order_postmeta
						ON acsort_orders.ID = acsort_order_postmeta.post_id
						AND acsort_order_postmeta.meta_key = '_completed_date'
					";

		$query->query_orderby = "
					GROUP BY {$wpdb->users}.ID
					ORDER BY acsort_lastorder $order
				";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}