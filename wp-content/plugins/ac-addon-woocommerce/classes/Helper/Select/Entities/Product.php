<?php

namespace ACA\WC\Helper\Select\Entities;

use AC;
use ACP\Helper\Select;
use ACP\Helper\Select\Value;
use WP_Query;

class Product extends Select\Entities\Post {

	/**
	 * @var WP_Query
	 */
	protected $query;

	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Post();
		}

		$args = array_merge( [
			'post_type' => 'product',
		], $args );

		add_filter( 'posts_join', [ $this, 'join_postmeta' ] );
		add_filter( 'posts_where', [ $this, 'add_sku_to_where_clause' ], 10, 2 );
		add_filter( 'posts_groupby', [ $this, 'group_post_ids' ] );

		parent::__construct( $args, $value );
	}

	public function join_postmeta( $join ) {
		global $wpdb;

		remove_filter( 'posts_join', __FUNCTION__ );

		$join .= " INNER JOIN {$wpdb->postmeta} acpm ON {$wpdb->posts}.ID = acpm.post_id AND acpm.meta_key = '_sku'";

		return $join;
	}

	public function add_sku_to_where_clause( $where, WP_Query $wp_query ) {
		global $wpdb;

		remove_filter( 'posts_where', __FUNCTION__ );

		$where .= $wpdb->prepare( "OR acpm.meta_value LIKE '%%%s%%'", $wpdb->esc_like( $wp_query->query_vars['s'] ) );

		return $where;
	}

	/**
	 * @return string
	 */
	public function group_post_ids() {
		global $wpdb;

		remove_filter( 'posts_groupby', __FUNCTION__ );

		return $wpdb->posts . '.ID';
	}

}