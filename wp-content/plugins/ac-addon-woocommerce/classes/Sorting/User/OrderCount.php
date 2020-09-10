<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class OrderCount extends AbstractModel {

	/**
	 * @var array
	 */
	private $status;

	public function __construct( array $status = null ) {
		parent::__construct();

		$this->status = $status;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$order = $this->get_order();

		$query->query_fields .= ", COUNT( acsort_posts.ID ) AS acsort_ordercount";
		$query->query_from .= " LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON $wpdb->users.ID = acsort_postmeta.meta_value";
		$query->query_from .= " LEFT JOIN $wpdb->posts AS acsort_posts ON acsort_posts.ID = acsort_postmeta.post_id";
		$query->query_where .= " AND acsort_postmeta.meta_key = '_customer_user' AND acsort_posts.post_type = 'shop_order' ";

		if ( $this->status ) {
			$query->query_where .= sprintf( " AND acsort_posts.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->status ) ) );
		}

		$query->query_orderby = "
			GROUP BY acsort_postmeta.meta_value
			ORDER BY acsort_ordercount $order
		";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}