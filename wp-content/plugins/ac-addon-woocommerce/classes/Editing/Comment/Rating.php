<?php

namespace ACA\WC\Editing\Comment;

use ACP;
use WC_Comments;

class Rating extends ACP\Editing\Model\Meta {

	public function get_edit_value( $id ) {
		$comment = get_comment( $id );

		if ( 'product' !== get_post_type( $comment->comment_post_ID ) ) {
			return null;
		}

		return $this->column->get_raw_value( $id );
	}

	public function get_view_settings() {
		$options = [
			'' => __( 'None', 'codepress-admin-columns' ),
		];

		for ( $i = 1; $i < 6; $i++ ) {
			$options[ $i ] = $i;
		}

		return [
			'type'         => 'select',
			'options'      => $options,
			'clear_button' => true,
		];
	}

	public function save( $id, $value ) {
		$value = absint( $value );

		if ( $value > 5 ) {
			return false;
		}

		$result = parent::save( $id, $value );

		$comment = get_comment( $id );
		$product = wc_get_product( $comment->comment_post_ID );

		// Update average rating for product
		WC_Comments::get_average_rating_for_product( $product );

		return $result;
	}

}