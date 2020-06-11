<?php

defined( 'ABSPATH' ) or die();

// //UNCOMMENT THE FOLLOWING LINE TO PERFORM THE API EXAMPLE (will output in wp_footer):
//require_once( 'wjecf-pro-api-example.php');


/**
 * API Functions for public use
 *
 * Call the API by using WJECF_API()->api_function();
 *
 */
class WJECF_Pro_API {


	/**
	 * Get a coupon code (source can be an code, an id, or a WC_Coupon object)
	 * Validity of the code will not verified by this function.
	 *
	 * @since 2.5.1 Public
	 * @since 2.3.0 Introduced as protected function
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 *
	 * @return string The coupon code
	 */
	public function get_coupon_code( $coupon ) {
		if ( is_string( $coupon ) && ! is_numeric( $coupon ) ) {
			return $coupon;
		}
		return WJECF_WC()->get_coupon( $coupon )->get_code();
	}

	/**
	 * The total quantity of the products in the cart that match the coupon restrictions
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return int Quantity of matching products
	 */
	public function get_quantity_of_matching_products( $coupon ) {
		return WJECF()->get_quantity_of_matching_products( $coupon );
	}

	/**
	 * The total value of the products in the cart that match the coupon restrictions
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return int Subtotal of matching products
	 */
	public function get_subtotal_of_matching_products( $coupon ) {
		return WJECF()->get_subtotal_of_matching_products( $coupon );
	}

	/**
	 * Verifies whether the coupon applies to the given product.
	 * This function will return false for Free Products (unlike WC_Coupon->is_valid_for_product )
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @param WC_Product $product The product
	 * @param array $values Optional cart_item_data
	 *
	 * @return bool True if valid for the product, otherwise false
	 *
	 */
	public function coupon_is_valid_for_product( $coupon, $product, $values = array() ) {
		return WJECF()->coupon_is_valid_for_product( $coupon, $product, $values );
	}

	/**
	 * Get array of the selected shipping methods ids.
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array Id's of the shipping methods or an empty array.
	 */
	public function get_coupon_shipping_method_ids( $coupon ) {
		return WJECF()->get_coupon_shipping_method_ids( $coupon );
	}


	/**
	 * Get array of the selected payment method ids.
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array  Id's of the payment methods or an empty array.
	 */
	public function get_coupon_payment_method_ids( $coupon ) {
		return WJECF()->get_coupon_payment_method_ids( $coupon );
	}

	/**
	 * Get array of the selected customer ids.
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array  Id's of the customers (users) or an empty array.
	 */
	public function get_coupon_customer_ids( $coupon ) {
		return WJECF()->get_coupon_customer_ids( $coupon );
	}

	/**
	 * Get array of the selected customer role ids.
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array  Id's (string) of the customer roles or an empty array.
	 */
	public function get_coupon_customer_roles( $coupon ) {
		return WJECF()->get_coupon_customer_roles( $coupon );
	}

	/**
	 * Get array of the excluded customer role ids.
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array  Id's (string) of the excluded customer roles or an empty array.
	 */
	public function get_coupon_excluded_customer_roles( $coupon ) {
		return WJECF()->get_coupon_excluded_customer_roles( $coupon );
	}

	/**
	 * Include a template file, either from this plugins directory or overwritten in the themes directory
	 * @since 2.5.1 Introduced
	 * @param string $template_name
	 * @param array $variables The variables to include in the template [ 'name' => value ]
	 * @return void
	 */
	public function include_template( $template_name, $variables = array() ) {
		return WJECF()->include_template( $template_name, $variables );
	}

	// ===========================
	// PLUGIN: WJECF_AutoCoupon
	// ===========================

	/**
	 * Get an array of all the Auto Coupons, in order of priority
	 *
	 * @since 2.3.0 Introduced
	 * @return array An array [ coupon_code => WC_Coupon ]
	 */
	public function get_all_auto_coupons() {
		return WJECF()->get_plugin( 'autocoupon' )->get_all_auto_coupons();
	}

	/**
	 * API 2.3.4
	 * Get an array of all the queued coupons in the session
	 * (Queued coupons are coupons that the customer tried to apply; but was not yet valid)
	 *
	 * @since 2.3.4 Introduced
	 * @param bool $exclude_if_in_cart If true, the coupons that are applied in the cart will not be returned
	 * @return array An array [ coupon_code => WC_Coupon ]
	 */
	public function get_queued_coupons( $exclude_if_in_cart = false ) {
		$coupon_codes = WJECF()->get_plugin( 'pro-coupon-queueing' )->get_queued_coupon_codes( $exclude_if_in_cart );
		$coupons      = array();
		foreach ( $coupon_codes as $coupon_code ) {
			$coupons[ $coupon_code ] = new WC_Coupon( $coupon_code );
		}
		return $coupons;
	}

	/**
	 * @deprecated
	 */
	public function get_by_url_coupons() {
		_deprecated_function( 'get_by_url_coupons', '2.3.4', 'get_queued_coupons' );
		return $this->get_queued_coupons();
	}


	// ===========================
	// PLUGIN: WJECF_Pro_Free_Products
	// ===========================

	/**
	 * Get array of the free product ids.
	 *
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @since 2.3.0 Introduced
	 * @return array Id's of the free products or an empty array.
	 */
	public function get_coupon_free_product_ids( $coupon ) {
		$coupon = WJECF_WC()->get_coupon( $coupon );
		return WJECF()->get_plugin( 'pro-free-products' )->get_coupon_free_product_ids( $coupon );
	}

	/**
	 * Checks whether the user must choose a free product if this coupon is applied
	 *
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @since 2.3.0 Introduced
	 * @return bool True if the user must select a free product
	 */
	public function must_select_free_product( $coupon ) {
		$coupon = WJECF_WC()->get_coupon( $coupon );
		return WJECF()->get_plugin( 'pro-free-products' )->must_select_free_product( $coupon );
	}

	/**
	 * Get the 'select free gift'-message.
	 *
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @param string $context 'raw' or 'view'. If view (default) is used, the translated value will be retrieved
	 * @return string|bool will be false if raw and empty.
	 */
	public function get_select_free_product_message( $coupon, $context = 'view' ) {
		return WJECF()->get_plugin( 'pro-free-products' )->get_select_free_product_message( $coupon, $context );
	}

	/**
	 * Get the 'select a free gift'-coupons that are currently in the cart
	 *
	 * @since 2.3.0 Introduced
	 * @return array The WC_Coupon objects
	 */
	public function get_applied_select_free_product_coupons() {
		return WJECF()->get_plugin( 'pro-free-products' )->get_applied_select_free_product_coupons();
	}

	/**
	 * Get the id of the selected free gift for the coupon
	 *
	 * @deprecated Since 2.5.1. Use get_session_selected_products instead.
	 * @since 2.3.0 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return int|bool The free product, or false if none selected
	 */
	public function get_session_selected_product( $coupon ) {
		_deprecated_function( 'get_session_selected_product', '2.5.1', 'get_session_selected_products' );

		$result = $this->get_session_selected_products( $coupon );
		if ( empty( $result ) ) {
			return false;
		}
		$form_item = current( $result );
		return $form_item->getProductId();
	}

	/**
	 * Get the product_ids and quantities of the selected free gifts for the coupon
	 *
	 * Quantity can be a numeric value or simply 'true'
	 *
	 * @since 2.5.1 Introduced
	 * @param int|string|WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array An array with the free products [ product_id => WJECF_Free_Product_Form_Item ]
	 */
	public function get_session_selected_products( $coupon ) {
		$coupon_code = $this->get_coupon_code( $coupon );
		return WJECF()->get_plugin( 'pro-free-products' )->get_session_selected_products( $coupon_code );
	}


	// ===========================
	// DEBUGGING
	// ===========================

	/**
	 * API 2.5.1
	 * Log a message for debugging.
	 *
	 * If debug_mode is false; messages with level 'debug' will be ignored.
	 *
	 * @since 2.3.0
	 * @param string $level The level of the message. e.g. 'debug' or 'warning'
	 * @param string $string The message to log
	 */
	public function log( $level, $message = null ) {
		//Backwards compatibility; $level was introduced in 2.5.1
		if ( is_null( $message ) ) {
			$message = $level;
			$level   = 'debug';
		}
		WJECF()->log( $level, $message, 1 );
	}

	// ===========================
	// END OF API FUNCTIONS
	// ===========================



	/**
	 * Singleton Instance
	 *
	 * @static
	 * @return Singleton Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	protected static $_instance = null;
}
