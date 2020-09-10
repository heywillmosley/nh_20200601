<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class Ratings extends AbstractModel {

	/**
	 * @var string 'AVG' or 'COUNT
	 */
	private $sort_type;

	public function __construct( $sort_type = null ) {
		parent::__construct();

		if ( null === $sort_type ) {
			$sort_type = 'COUNT';
		}

		$this->sort_type = $sort_type;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$order = $this->get_order();

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$affix = 'AVG' === $this->sort_type
			? 'AVG'
			: 'COUNT';

		$query->query_fields .= sprintf( ", %s( acsort_commentmeta.meta_value ) AS ac_ratingcount", esc_sql( $affix ) );
		$query->query_from .= "
			{$join_type} JOIN $wpdb->comments AS acsort_comments ON acsort_comments.user_id = $wpdb->users.ID
				AND acsort_comments.comment_approved = '1'
			{$join_type} JOIN $wpdb->commentmeta AS acsort_commentmeta ON acsort_commentmeta.comment_id = acsort_comments.comment_ID
				AND acsort_commentmeta.meta_key = 'rating'
			{$join_type} JOIN $wpdb->posts AS acsort_posts ON acsort_comments.comment_post_ID = acsort_posts.ID
				AND acsort_posts.post_type = 'product'
			";
		$query->query_orderby = "
			GROUP BY $wpdb->users.ID
			ORDER BY ac_ratingcount $order
		";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}