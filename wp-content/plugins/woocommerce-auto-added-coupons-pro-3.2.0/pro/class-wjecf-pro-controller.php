<?php

defined( 'ABSPATH' ) or die();

//require_once( 'wjecf-pro-evalmath.php' );

/**
 * Miscellaneous Pro functions
 */
class WJECF_Pro_Controller extends WJECF_Controller {


	public function __construct() {
		parent::__construct();
	}

	public function init_hooks() {
		parent::init_hooks();

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		//Coupon columns
		add_filter( 'manage_shop_coupon_posts_columns', array( $this, 'admin_shop_coupon_columns' ), 20, 1 );
		add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'admin_render_shop_coupon_columns' ), 2 );

		//Frontend hooks
		add_action( 'woocommerce_coupon_loaded', array( $this, 'woocommerce_coupon_loaded' ), 10, 1 );
		//Overwrite coupon error message
		add_filter( 'woocommerce_coupon_error', array( $this, 'filter_woocommerce_coupon_error' ), 10, 3 );

		//PRO coupon validations
		add_action( 'wjecf_coupon_can_be_applied', array( $this, 'wjecf_coupon_can_be_applied' ), 10, 2 );

	}

	/* ADMIN HOOKS */
	public function admin_init() {
		//Admin hooks

		add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'on_woocommerce_coupon_options_usage_restriction' ), 20, 1 );
		add_action( 'woocommerce_coupon_options_usage_limit', array( $this, 'on_woocommerce_coupon_options_usage_limit' ), 20, 1 );
		add_action( 'wjecf_woocommerce_coupon_options_extended_features', array( $this, 'admin_coupon_options_extended_features' ), 30, 2 );
	}


	/**
	 * Checks whether it's the first order for a customer.
	 *
	 * @since 2.5.6
	 * @return bool|null Returns null if the current user or billing email is not known!!!
	 */
	public function is_first_purchase() {
		$is_first_purchase = null; //Null means 'unknown'.

		//Once we found out that the customer has ordered before; remember it in the session so that the customer can't trick us just by changing the email address
		if ( $this->get_session( 'has_purchased_before' ) ) {
			$is_first_purchase = false;
		}

		if ( null === $is_first_purchase ) {
			$known_user = false;

			$order_statuses = array( 'wc-completed', 'wc-processing', 'wc-on-hold' );

			if ( isset( WC()->customer ) && WC()->customer->get_id() ) {
				$known_user = true;
				$orders     = wc_get_orders(
					array(
						'limit'       => 1,
						'customer_id' => WC()->customer->get_id(),
						'status'      => $order_statuses,
					)
				);
				if ( count( $orders ) > 0 ) {
					$this->set_session( 'has_purchased_before', true );
					$is_first_purchase = false;
				}
			}
		}

		if ( null === $is_first_purchase ) {
			$email_addresses = $this->get_user_emails();
			//Remember entered email addresses, because WC does not remember them unit posted checkout form is valid.
			if ( empty( $email_addresses ) ) {
				$email_addresses = $this->get_session( 'user_emails', array() );
			} else {
				$this->set_session( 'user_emails', $email_addresses );
			}

			foreach ( $email_addresses as $email_address ) {
				$known_user = true;
				$orders     = wc_get_orders(
					array(
						'limit'    => 1,
						'customer' => $email_address,
						'status'   => $order_statuses,
					)
				);
				if ( count( $orders ) > 0 ) {
					$this->set_session( 'has_purchased_before', true );
					$is_first_purchase = false;
					break;
				}
			}
		}

		if ( null === $is_first_purchase && $known_user ) {
			$is_first_purchase = true;
		}

		/**
		 * Is this the customer's first purchase?
		 *
		 * Please note that the value can be null if it's unknown whether it's a first purchase.
		 *
		 * @since 3.1.2
		 *
		 * @param bool|null  $is_first_purchase True if it's the first purchase, false if it's not the first purchase. Null if it's unknown.
		 */
		return apply_filters( 'wjecf_is_first_purchase', $is_first_purchase );
	}

	public function is_valid_on_first_purchase_only( $coupon ) {
		return $coupon->get_meta( '_wjecf_first_purchase_only' ) == 'yes';
	}

	public function wjecf_coupon_can_be_applied( $can_be_applied, $coupon ) {
		//Don't auto apply unless we know it's the first purchase of the user
		if ( $this->is_valid_on_first_purchase_only( $coupon ) && ! $this->is_first_purchase() ) {
			return false;
		}
		return $can_be_applied;
	}

	/**
	 * Execute the PRO validation rules for coupons. Throw an exception when not valid.
	 *
	 * @param WC_Coupon $coupon
	 */
	public function validate_pro( $coupon ) {
		$this->validate_first_purchase_only( $coupon );
	}

	/**
	 * Validate 'first purchase only'.
	 *
	 * @param WC_Coupon $coupon
	 */
	private function validate_first_purchase_only( $coupon ) {
		if ( $this->is_valid_on_first_purchase_only( $coupon ) ) {
			$is_first_purchase = $this->is_first_purchase();
			if ( false === $is_first_purchase ) { //null if user data is not yet known
				throw new Exception(
					/* translators: 1: coupon code */
					sprintf( __( 'Sorry, coupon "%s" is only valid on your first purchase.', 'woocommerce-jos-autocoupon' ), $coupon->get_code() ),
					self::E_WC_COUPON_FIRST_PURCHASE_ONLY
				);
			}
		}
	}

	//Admin

	// //Tab 'misc'
	public function admin_coupon_options_extended_features( $thepostid, $post ) {
		echo '<h3>' . __( 'Custom error message', 'woocommerce-jos-autocoupon' ) . '</h3>';

		woocommerce_wp_textarea_input(
			array(
				'id'          => '_wjecf_custom_error_message',
				'label'       => __( 'Custom error message', 'woocommerce-jos-autocoupon' ),
				'description' => __( 'This message will be displayed when the customer tries to apply this coupon when it is invalid. Leave empty to use the default message.', 'woocommerce-jos-autocoupon' ),
				'desc_tip'    => true,
			)
		);
	}

	//since 2.5.0 moved to the 'Usage restriction' tab
	public function on_woocommerce_coupon_options_usage_restriction() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		echo '<div class="options_group wjecf_hide_on_product_discount">';
		echo '<h3>' . __( 'Discount on cart with excluded products', 'woocommerce-jos-autocoupon' ) . '</h3>';

		//=============================
		//2.2.3 Allow even if excluded items in cart
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wjecf_allow_cart_excluded',
				'label'       => __( 'Allow discount on cart with excluded items', 'woocommerce-jos-autocoupon' ),
				'description' => __( 'Check this box to allow a \'Cart Discount\' coupon to be applied even when excluded items are in the cart. Useful when using the subtotal/quantity of matching products for a cart discount.', 'woocommerce-jos-autocoupon' ),
				'desc_tip'    => true,
			)
		);
		echo '</div>';
	}

	public function on_woocommerce_coupon_options_usage_limit() {
		//=============================
		//2.5.6 First time customers only
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wjecf_first_purchase_only',
				'label'       => __( 'First purchase only', 'woocommerce-jos-autocoupon' ),
				'description' => __( 'Check this box to limit this coupon to the first purchase of a customer only. (Verified by billing email address or user id)', 'woocommerce-jos-autocoupon' ),
			)
		);
	}

	public function admin_coupon_meta_fields( $coupon ) {
		//$fields = parent::admin_coupon_meta_fields();
		return array(
			//2.2.3
			'_wjecf_allow_cart_excluded' => 'yesno',
			//2.5.6
			'_wjecf_first_purchase_only' => 'yesno',
			//3.0.7
			'_wjecf_custom_error_message' => 'clean',
		);
	}

	private $inject_coupon_columns = array();
	/**
	 * Inject custom columns on the Coupon Admin Page
	 *
	 * @param string $column_key The key to identify the column
	 * @param string $caption The title to show in the header
	 * @param callback $callback The function to call when rendering the column value ( Will be called with parameters $column_key, $post )
	 * @param string $after_column Optional, The key of the column after which the column should be injected, if omitted the column will be placed at the end
	 */
	public function inject_coupon_column( $column_key, $caption, $callback, $after_column = null ) {
		$this->inject_coupon_columns[ $column_key ] = array(
			'caption'  => $caption,
			'callback' => $callback,
			'after'    => $after_column,
		);
	}

	/**
	 * Custom columns on coupon admin page
	 *
	 * @param array $columns
	 */
	public function admin_shop_coupon_columns( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $column ) {
			$new_columns[ $key ] = $column;
			foreach ( $this->inject_coupon_columns as $inject_key => $inject_column ) {
				if ( $inject_column['after'] == $key ) {
					$new_columns[ $inject_key ] = $inject_column['caption'];
				}
			}
		}
		foreach ( $this->inject_coupon_columns as $inject_key => $inject_column ) {
			if ( is_null( $inject_column['after'] ) || ! isset( $columns[ $inject_column['after'] ] ) ) {
				$new_columns[ $inject_key ] = $inject_column['caption'];
			}
		}
		return $new_columns;
	}

	/**
	 * Output custom columns for coupons
	 *
	 * @param string $column
	 */
	public function admin_render_shop_coupon_columns( $column ) {
		global $post;
		if ( isset( $this->inject_coupon_columns[ $column ]['callback'] ) ) {
			call_user_func( $this->inject_coupon_columns[ $column ]['callback'], $column, $post );
		}
	}

	//Frontend

	public function woocommerce_coupon_loaded( $coupon ) {
		if ( $this->allow_overwrite_coupon_values() ) {
			//2.2.3 Allow coupon even if excluded products are in the cart
			//This way we can use the subtotal/quantity of matching products for a cart discount
			$allow_cart_excluded = $coupon->get_meta( '_wjecf_allow_cart_excluded' ) == 'yes';
			if ( $allow_cart_excluded && $coupon->is_type( WJECF_WC()->wc_get_cart_coupon_types() ) ) {
				//HACK: Overwrite the exclusions so WooCommerce will allow the coupon
				//These values are used in the WJECF_Controller->coupon_is_valid_for_product
				$coupon->set_excluded_product_ids( array() );
				$coupon->set_excluded_product_categories( array() );
				$coupon->set_exclude_sale_items( false );
			}
		}
	}

	public function filter_woocommerce_coupon_error( $error_message, $err_code, $coupon ) {
		//Can be null in occasions
		if ( $coupon instanceof WC_Coupon ) {
			$custom_message = $coupon->get_meta( '_wjecf_custom_error_message' );
			if ( $custom_message ) {
				$error_message = $custom_message;
			}
		}
		return $error_message;
	}

	// Templating

	/**
	 * Get overwritable template filename
	 *
	 * Template can be overwritten in wp-content/themes/YOUR_THEME/woocommerce-auto-added-coupons/
	 * @param string $template_name
	 * @return string Template filename
	 */
	public function get_template_filename( $template_name ) {
		$template_path = 'woocommerce-auto-added-coupons';

		//Get template overwritten file
		$template = locate_template( trailingslashit( $template_path ) . $template_name );

		// Get default template
		if ( ! $template ) {
			$plugin_template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/';
			$template             = $plugin_template_path . $template_name;
		}

		return $template;
	}

	/**
	 * Include a template file, either from this plugins directory or overwritten in the themes directory
	 * @param string $template_name
	 * @return void
	 */
	public function include_template( $template_name, $variables = array() ) {
		/* phpcs:ignore */
		extract( $variables );
		include( $this->get_template_filename( $template_name ) );
	}
}
