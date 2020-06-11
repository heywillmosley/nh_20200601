<?php // phpcs:ignore

defined( 'ABSPATH' ) or die();

class WJECF_Pro_Free_Products extends Abstract_WJECF_Plugin {

	/**
	 * This meta is applied to identify a free product in the cart
	 */
	const META_KEY_FREE_PRODUCT = '_wjecf_free_product_coupon';

	protected $template = null;
	protected $admin    = null;

	public function __construct() {
		$this->set_plugin_data(
			array(
				'description'        => __( 'Allow free products to be added to the cart.', 'woocommerce-jos-autocoupon' ),
				'dependencies'       => array(),
				'admin_dependencies' => array( 'admin' ),
				'can_be_disabled'    => true,
			)
		);
	}

	// =============
	// BEGIN ADMIN
	// =============

	public function init_admin_hook() {
		require_once( 'class-wjecf-pro-free-products-admin.php' );
		$this->admin = new WJECF_Pro_Free_Products_Admin( $this );
		$this->admin->init_admin_hook();
	}

	public function admin_coupon_meta_fields( $coupon ) {
		return $this->admin->admin_coupon_meta_fields( $coupon );
	}

	// =============
	// END ADMIN
	// =============

	public function init_hook() {
		if ( ! class_exists( 'WC_Coupon' ) ) {
			return;
		}

		$this->init_free_product_form();

		//Frontend hooks - logic

		// Must be AFTER hook of Auto Coupon (5) but BEFORE WC_Cart_Session::set_session (10)
		WJECF()->safe_add_action( 'woocommerce_after_calculate_totals', array( $this, 'update_free_products_in_cart' ), 8 );

		//Set price to 0.00 for free products
		add_filter( 'woocommerce_add_cart_item', array( $this, 'filter_woocommerce_add_cart_item' ), PHP_INT_MAX, 6 ); // mark the free products as such
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'filter_woocommerce_get_cart_item_from_session' ), PHP_INT_MAX, 3 ); // mark the free products as such

		// overwrite values (price 0.00) if it's a free product
		add_filter( 'woocommerce_product_get_price', array( $this, 'filter_woocommerce_product_get_price' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_product_variation_get_price', array( $this, 'filter_woocommerce_product_get_price' ), PHP_INT_MAX, 2 );

		// overwrite subtotal of 0.00
		add_filter( 'woocommerce_coupon_discount_amount_html', array( $this, 'filter_woocommerce_coupon_discount_amount_html' ), 8, 2 );

		//A free product coupon always has a value (for Auto coupon)
		add_filter( 'wjecf_coupon_has_a_value', array( $this, 'filter_wjecf_coupon_has_a_value' ), 10, 2 ); // if coupon grants free products, it has a value! Required for Auto Coupons
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'filter_woocommerce_coupon_is_valid_for_product' ), 10, 4 ); //don't count the free items for coupon restrictions

		add_action( 'woocommerce_cart_emptied', array( $this, 'woocommerce_cart_emptied' ) );
	}

	/**
	 * Init the 'Select free gift'-form
	 * @return type
	 */
	private function init_free_product_form() {
		require_once( 'class-wjecf-free-product-form-item.php' );
		require_once( 'class-wjecf-pro-free-products-template.php' );

		$this->template = new WJECF_Pro_Free_Products_Template( $this );
		$this->template->init_hook();

		//2.3.4-b6 AJAX support for 'Select free gift'
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'filter_woocommerce_update_order_review_fragments' ), 10, 1 );
		add_action( 'wc_ajax_wjecf_cart_select_free_product', array( $this, 'wc_ajax_wjecf_cart_select_free_product' ) );

		//3.0.0 Auto-cart-refresh (in combination with js) after automatic coupon applied/removed
		add_action( 'wjecf_after_update_matched_autocoupons', array( $this, 'action_wjecf_after_update_matched_autocoupons' ) );

		//Frontend hooks for 'Select free gift'-form
		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'render_checkout_select_free_product' ) );
		add_action( 'woocommerce_cart_contents', array( $this, 'render_cart_select_free_product' ) );

		//Process 'Select free gift'-form
		add_filter( 'woocommerce_checkout_update_order_review', array( $this, 'woocommerce_checkout_update_order_review' ) );
		add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'woocommerce_update_cart_action_cart_updated' ) );
		add_action( 'woocommerce_before_checkout_process', array( $this, 'woocommerce_before_checkout_process' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10 );
	}

	public function filter_wjecf_coupon_has_a_value( $has_a_value, $coupon ) {
		//Tell autocoupon that the coupon has a value, if it is a free product coupon
		if ( ! $has_a_value && count( $this->get_coupon_free_product_ids( $coupon ) ) > 0 ) {
			$has_a_value = true;
		} elseif ( $coupon->get_meta( '_wjecf_bogo_matching_products' ) == 'yes' ) {
			$bogo_products = $this->get_bogo_products_from_cart( $coupon );
			$has_a_value   = ! empty( $bogo_products );
		}
		return $has_a_value;
	}

	//don't count the free items for coupon restrictions
	public function filter_woocommerce_coupon_is_valid_for_product( $valid, $product, $coupon, $values = null ) {
		if ( $valid && $this->is_free_product( $product ) ) {
			$valid = false;
		}
		return $valid;
	}

	/**
	 * Overwrite product data if it belongs to a free product coupon
	 */
	public function filter_woocommerce_add_cart_item( $cart_item_data, $cart_item_key ) {
		if ( $this->is_free_cart_item( $cart_item_data ) ) {
			$this->tag_as_free_product( $cart_item_data['data'] );
		}
		return $cart_item_data;
	}

	/**
	 * Overwrite product data if it belongs to a free product coupon
	 */
	public function filter_woocommerce_get_cart_item_from_session( $session_data, $values, $key ) {
		if ( $this->is_free_cart_item( $session_data ) ) {
			$this->tag_as_free_product( $session_data['data'] );
		}
		return $session_data;
	}

	/**
	 * Overwrite the product price, if it belongs to a free product coupon
	 * @param type|string $price
	 * @param type|string $product
	 * @return type
	 */
	public function filter_woocommerce_product_get_price( $price = '', $product = '' ) {
		if ( $this->is_free_product( $product ) ) {
			return 0;
		}
		return $price;
	}

	/**
	 * Overwrite discount amount html for a zero-value free product coupon.
	 *
	 * @param  string $discount_amount_html The html created by wc_cart_totals_coupon_html()
	 * @param  WC_Coupon $coupon The coupon
	 * @return string The overwritten html
	 */
	public function filter_woocommerce_coupon_discount_amount_html( $discount_amount_html, $coupon ) {
		if ( $this->is_free_product_coupon( $coupon ) ) {
			$amount = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );

			//No discount and no free shipping? Then hide the discount amount of -0.00
			if ( ! $coupon->get_free_shipping() && empty( $amount ) ) {
				$discount_amount_html = '';
			}
		}
		return $discount_amount_html;
	}

	//Forget all selected products after successful checkout
	public function woocommerce_cart_emptied() {
		$this->clear_session_selected_products();
	}

	//Save selected free gift in the session
	public function woocommerce_update_cart_action_cart_updated( $cart_updated ) {
		if ( $this->process_form() ) {
			$cart_updated = true;
		}
		return $cart_updated;
	}

	public function woocommerce_checkout_update_order_review( $post_data_string ) {
		//TODO: Better handling of checkout form
		parse_str( $post_data_string, $post_data );
		$this->process_form( $post_data );
	}

	public function woocommerce_before_checkout_process() {
		$this->process_form();

		// //Don't checkout if no free gift selected
		// foreach( WC()->cart->get_applied_coupons() as $coupon_code ) {
		//     $coupon = new WC_Coupon( $coupon_code );
		//     //Must the user select a free product?
		//     if ( ! $this->must_select_free_product( $coupon ) ) {
		//         continue;
		//     }

		//     $session_selected_products = $this->get_session_selected_products( $coupon_code );
		//     $selected_free_products = $this->count_selected_products( $coupon, $session_selected_products );
		//     if ( empty( $selected_free_products ) ) {
		//         wc_add_notice( __('Please select your free gift.', 'woocommerce-jos-autocoupon'), 'error' );
		//         return;
		//     }
		// }
	}

	public function wp_enqueue_scripts() {
		//2.5.4 Only load script on cart or checkout page
		if ( ! is_cart() && ! is_checkout() ) {
			return;
		}

		//2.5.1 Moved JS to an external script
		wp_enqueue_script( 'wjecf-free-products', WJECF()->plugin_url() . 'pro/plugins/WJECF_Pro_Free_Products/js/wjecf-free-products.js', array( 'jquery' ), WJECF()->plugin_version() );
		wp_enqueue_style( 'wjecf-style', WJECF()->plugin_url( 'assets/wjecf.css' ), array(), WJECF()->plugin_version() );
	}

	//Show 'select free gift'-radiobuttons during checkout
	public function render_checkout_select_free_product() {
		$variables = $this->get_form_variables();
		$this->template->render_template( 'checkout/select-free-product.php', $variables );
	}

	//Show 'select free gift'-radiobuttons on cart
	public function render_cart_select_free_product() {
		$variables = $this->get_form_variables();
		$this->template->render_template( 'cart/select-free-product.php', $variables );
	}

	/**
	 * (Since 2.3.4-b6)
	 * Get Select free product AJAX fragment
	 */
	public function wc_ajax_wjecf_cart_select_free_product() {
		$this->render_cart_select_free_product();
		wp_die();
	}

	/**
	 * After auto-coupons have changed set a cookie with the active free product coupons.
	 * This cookie is used on the frontend to detect changes in the active free product coupons (cart must be refreshed)
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function action_wjecf_after_update_matched_autocoupons() {
		if ( headers_sent() ) {
			return;
		}

		$free_product_coupons = array();
		foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
			$coupon = new WC_Coupon( $coupon_code );
			if ( count( $this->get_coupon_free_product_ids( $coupon ) ) > 0 ) {
				$free_product_coupons[] = $coupon_code;
			}
		}
		//Note: cookie expires at browser close.
		wc_setcookie( 'wjecf_free_product_coupons', json_encode( $free_product_coupons ) );
	}

	/**
	 * (Since 2.3.4-b6)
	 * Add Select free product to the AJAX fragments
	 *
	 * @param array $fragments
	 * @return array Updated fragments
	 */
	public function filter_woocommerce_update_order_review_fragments( $fragments ) {
		ob_start();
		$this->render_checkout_select_free_product();
		$fragments['.wjecf-fragment-checkout-select-free-product'] = ob_get_clean();

		return $fragments;
	}


	/**
	 * The variables that will be used by the 'Select free gift'-form
	 * @return array
	 */
	private function get_form_variables() {
		$free_gift_coupons = $this->get_applied_select_free_product_coupons();

		$coupons_form_data = array();

		$field_idx = 0;
		foreach ( $free_gift_coupons as $coupon ) {
			$coupon_code             = $coupon->get_code();
			$selected_products       = $this->get_session_selected_products( $coupon_code );
			$coupon_free_product_ids = $this->get_coupon_free_product_ids( $coupon );
			$allow_multiple_products = $this->allow_multiple_products( $coupon );
			//Amount of items selected (used on the checkout review page. Only show selection if nothing was selected)
			$selected_quantity = array_sum( $this->count_selected_products( $coupon, $selected_products ) );

			//Join the items selected by the customer with all the selectable free items
			$form_items = array();

			//Keep track of the product ids already added to the $form_items array
			$found_product_ids = array();
			foreach ( $coupon_free_product_ids as $product_id ) {
				$found = false; // selecter for the product found?

				//There can be multiple selectors for a product
				do {
					$form_item = $this->find_form_item( $product_id, $selected_products );

					if ( ! is_null( $form_item ) ) {
						if ( $form_item->getQuantity() > 0 || $form_item->hasAttributes() ) {
							$form_items[]        = $form_item;
							$found_product_ids[] = $product_id;
						}

						$key = array_search( $form_item, $selected_products );
						unset( $selected_products[ $key ] );
					}

					//Ensure that non-selected products are also in the array; otherwise they won't be rendered by the template!
					if ( ! in_array( $product_id, $found_product_ids ) ) {
						$found_product_ids[] = $product_id;
						$form_items[]        = new WJECF_Free_Product_Form_Item( $product_id );
					}
				} while ( ! is_null( $form_item ) );
			}

			// Since 2.6.0: Preload the DOM ids and names to keep the template clean
			$name_prefix  = sprintf( 'wjecf_free_sel[%d]', $field_idx );
			$id_prefix    = sprintf( 'wjecf_free_sel_%d', $field_idx );
			$totalizer_id = $id_prefix . '_total_qty';
			$max_quantity = $this->get_free_product_amount_for_coupon( $coupon );

			foreach ( $form_items as $key => $form_item ) {
				$product = $form_item->getProduct();
				//Don't display items that are out of stock
				if ( ! $product instanceof WC_Product || ! $product->is_in_stock() || ! $product->is_purchasable() ) {
					unset( $form_items[ $key ] );
					continue;
				}

				// Since 2.6.0 Preload the DOM ids and names to keep the template clean
				$form_item->name_prefix       = $name_prefix;
				$form_item->field_name_prefix = "{$name_prefix}[products][{$key}]"; // e.g. wjecf_sel[0][product][0]
				$form_item->id_prefix         = $id_prefix;
				$form_item->field_id          = esc_attr( $id_prefix . '_' . $key ); // e.g. wjecf_sel_0_0
				$form_item->totalizer_id      = $totalizer_id;
			}

			$coupons_form_data[ $coupon_code ] = array(
				'coupon'                  => $coupon,
				'coupon_code'             => $coupon_code,
				'allow_multiple_products' => $allow_multiple_products,
				'form_items'              => $form_items,
				'selected_quantity'       => $selected_quantity,
				'max_quantity'            => $max_quantity,
				'name_prefix'             => $name_prefix,
				'id_prefix'               => $id_prefix,
				'totalizer_id'            => $totalizer_id,
			);

			$field_idx++;
		}
		$variables = compact(
			'free_gift_coupons', //Legacy
			'coupons_form_data'
		);

		return $variables;
	}

	/**
	 * Find the form_item with the give product id in the array.
	 * Returns the form item if found, otherwise null
	 * @param int $product_id
	 * @param array $form_items
	 * @return WJECF_Free_Product_Form_Item|null
	 */
	private function find_form_item( $product_id, $form_items ) {
		foreach ( $form_items as $form_item_key => $form_item ) {
			if ( $form_item->getProductId() == $product_id ) {
				return $form_item;
			}
		}
		return null;
	}

	/**
	 * Get the POST variables for free product selection, save them to the session.
	 *
	 * @param array $post_data
	 * @return bool True if products are selected by the customer
	 */
	private function process_form( $post_data = null ) {
		if ( ! isset( $post_data ) ) {
			$post_data = $_POST;
		}
		$items_selected = false;

		//Support for templates prior to 2.5.1 ( had wjecf_free_sel_COUPONCODE instead of wjecf_free_sel[] )
		if ( isset( $post_data['wjecf_free_sel'] ) ) {
			//2.5.1
			$wjecf_free_sel = $post_data['wjecf_free_sel'];
			foreach ( $wjecf_free_sel as $coupon_form_data ) {
				if ( ! isset( $coupon_form_data['coupon'] ) || ! isset( $coupon_form_data['products'] ) ) {
					continue; //invalid form data
				}

				$coupon_code = $coupon_form_data['coupon'];

				//Convert the array to WJECF_Free_Product_Form_Item objects
				$form_items = $this->arrays_to_form_items( $coupon_form_data['products'] );

				//This part is for <input type="radio">
				//Radiobutton yields only an id; convert to a form_item
				if ( isset( $coupon_form_data['selected_product'] ) ) {
					$product_id = intval( $coupon_form_data['selected_product'] );
					//If product has variations, it will already be in the form_items array
					$form_item = $this->find_form_item( $product_id, $form_items );
					if ( is_null( $form_item ) ) {
						$form_item    = new WJECF_Free_Product_Form_Item( $product_id );
						$form_items[] = $form_item;
					}
					$form_item->setQuantity( WJECF_Free_Product_Form_Item::MAX_QUANTITY );
				}

				$this->set_session_selected_products( $coupon_code, $form_items );
				$items_selected = true;
			}
		} else {
			//Legacy: For templates prior to 2.5.1
			foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
				$field_name = 'wjecf_free_sel_' . esc_attr( $coupon_code );
				if ( isset( $post_data[ $field_name ] ) ) {
					$this->log( 'warning', 'The use of wjecf_free_sel_{$coupon_code} is deprecated since 2.5.1.' );
					$product_id = intval( $post_data[ $field_name ] );
					$form_item  = new WJECF_Free_Product_Form_Item( $product_id );
					$form_item->setQuantity( WJECF_Free_Product_Form_Item::MAX_QUANTITY );
					$this->set_session_selected_products( $coupon_code, array( $form_item ) );
					$items_selected = true;
				}
			}
		}

		//Validate the form items; notify errors
		$free_gift_coupons = $this->get_applied_select_free_product_coupons();
		foreach ( $free_gift_coupons as $coupon ) {
			$form_items = $this->get_session_selected_products( $coupon->get_code() );
			foreach ( $form_items as $form_item ) {
				$quantity = $form_item->getQuantity();
				if ( $quantity <= 0 ) {
					continue;
				}

				//Valid product?
				$product = $form_item->getProduct();
				if ( ! $product || ! $product->is_purchasable() ) {
					$this->log( 'error', 'Invalid product in form_item: ' . $form_item->getProductId() );
					continue;
				}

				//Is variation selected?
				$product_or_variation = $form_item->getProductOrVariation();
				if ( ! $product_or_variation ) {
					$this->template->notify_select_variation( $form_item->getProduct() );
					continue;
				}

				//Is stock sufficient?
				$quantity = $this->validate_free_product_quantity( $product_or_variation, $quantity );
				if ( $quantity < $form_item->getQuantity() ) {
					$this->template->notify_not_enough_stock( $product_or_variation );
					$form_item->setQuantity( $quantity );
				}
			}
		}

		return $items_selected;
	}

	/**
	 * Convert array of arrays to an array of WJECF_Free_Product_Form_Item objects
	 * @param array $form_product_data [ [ product_id, => quantity => , attributes => ], ... ]
	 * @return array Array: [ WJECF_Free_Product_Form_Item ]
	 */
	private function arrays_to_form_items( $form_product_data ) {
		$form_items = array();
		foreach ( $form_product_data as $product_data ) {
			try {
				$form_items[] = new WJECF_Free_Product_Form_Item( $product_data );
			} catch ( Exception $ex ) {
				$this->log( 'error', 'Invalid form data: ' . print_r( $product_data, true ) );
			}
		}
		return $form_items;
	}

	private function get_bogo_products_from_cart( $coupon ) {
		$bogos = array();

		$bogo = $coupon->get_meta( '_wjecf_bogo_matching_products' ) == 'yes';
		if ( $bogo ) {
			$coupon_multiplies = $this->allow_multiple_products( $coupon );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$product = $cart_item['data'];
				if ( ! $this->is_free_product( $product ) ) {
					if ( WJECF()->coupon_is_valid_for_product( $coupon, $product, $cart_item ) ) {
						$prod_or_var_id           = $product->get_id();
						$bogos[ $prod_or_var_id ] = $coupon_multiplies ? $cart_item['quantity'] : 1;
					}
				}
			}
		}
		//$this->log( 'debug', "Bogos [" . $coupon->get_code() . "] : " . implode( ", ", $bogos ) );
		return $bogos;
	}

	public function update_free_products_in_cart() {

		$coupons = WC()->cart->get_coupons();

		//Count the free products that should be in the cart [ product_id => quantity ]
		$free_product_ids = array();

		//Count the bogo products that should be in the cart [ product_id => quantity ]
		$bogo_product_ids = array();

		foreach ( $coupons as $coupon ) {
			//FREE PRODUCT
			//Must user choose a gift? Then get an array of chosen gifts
			if ( $this->must_select_free_product( $coupon ) ) {
				//Total quantity of free items must be <= coupon multiplication value
				$prod_form_data_array   = $this->get_session_selected_products( $coupon->get_code() );
				$selected_free_products = $this->count_selected_products( $coupon, $prod_form_data_array );
			} else {
				//Every item the same quantity of times in the cart
				$selected_free_products  = array();
				$coupon_qty              = $this->get_free_product_amount_for_coupon( $coupon );
				$coupon_free_product_ids = $this->get_coupon_free_product_ids( $coupon );
				foreach ( $coupon_free_product_ids as $product_id ) {
					$selected_free_products[ $product_id ] = $coupon_qty;
				}
			}
			foreach ( $selected_free_products as $product_id => $quantity ) {
				if ( isset( $free_product_ids[ $product_id ] ) ) {
					$free_product_ids[ $product_id ] += $quantity;
				} else {
					$free_product_ids[ $product_id ] = $quantity;
				}
			}

			//BOGO
			foreach ( $this->get_bogo_products_from_cart( $coupon ) as $product_id => $qty ) {
				$qty = apply_filters( 'wjecf_bogo_product_amount_for_coupon', $qty, $coupon ); // 2.3.3
				//If multiple rules, get the highest qty
				if ( ! isset( $bogo_product_ids[ $product_id ] ) || $bogo_product_ids[ $product_id ] < $qty ) {
					$bogo_product_ids[ $product_id ] = $qty;
				}
			}
		}

		//Merge bogos with the free_product_ids array
		foreach ( $bogo_product_ids as $product_id => $qty ) {
			if ( isset( $free_product_ids[ $product_id ] ) ) {
				$free_product_ids[ $product_id ] += $qty;
			} else {
				$free_product_ids[ $product_id ] = $qty;
			}
		}

		//NOW WE KNOW THE QUANTITY OF FREE PRODUCTS THAT SHOULD BE IN THE CART

		// Remove free products that don't apply anymore
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			if ( $this->is_free_product( $product ) ) {
				$prod_or_var_id = $product->get_id();

				if ( ! isset( $free_product_ids[ $prod_or_var_id ] ) ) {
					WC()->cart->remove_cart_item( $cart_item_key );
				}
			}
		}

		// Add free products or adjust the quantity
		foreach ( $free_product_ids as $product_id => $qty ) {
			$this->set_free_product_amount_in_cart( $product_id, $qty );
		}
	}

	/**
	 * Verifies the quantity is sufficient.
	 * Returns $quantity if that amount can be ordered, otherwise return the max amount that can be added to the cart.
	 * @param WC_Product $product
	 * @param int|bool $quantity
	 * @return int|bool The quantity that can be added to the cart.
	 */
	private function validate_free_product_quantity( $product, $quantity ) {
		$product_id = $product->get_id();

		//Make sure stock is sufficient
		$qty_in_cart = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == $product_id && ! $this->is_free_cart_item( $cart_item ) ) {
				$qty_in_cart += $cart_item['quantity'];
			}
		}

		$valid_quantity = $product->is_sold_individually() ? 1 : $this->product_available_stock( $product, $quantity + $qty_in_cart );
		$valid_quantity = max( 0, $valid_quantity - $qty_in_cart );

		return is_bool( $quantity ) ? (bool) $valid_quantity : $valid_quantity;
	}

	/**
	 * If the answer is no, it returns the available amount, otherwise it returns the requested amount
	 */
	private function product_available_stock( $product, $quantity ) {
		if ( ! $product->is_in_stock() ) {
			return 0;
		}
		if ( ! $product->has_enough_stock( $quantity ) ) {
			return min( $quantity, $product->get_stock_quantity() );
		}
		return $quantity;
	}

	/**
	 * Adds the item to the cart as a free product. If already in the cart it adjuests the quantity
	 *
	 * @param int $product_id The id of the product or varation
	 * @param int $quantity The amount to add
	 * @return string The cart_item_key
	 */
	private function set_free_product_amount_in_cart( $product_id, $quantity ) {
		//$this->log( 'debug', "( $product_id, $quantity )" );

		// Ensure we don't add a variation to the cart directly by variation ID
		if ( 'product_variation' == get_post_type( $product_id ) ) {
			$variation_id = $product_id;
			$product_id   = wp_get_post_parent_id( $variation_id );
			$variation    = WJECF_WC()->wc_get_product_variation_attributes( $variation_id );
		} else {
			$variation_id = 0;
			$variation    = array();
		}
		// Get the product
		$product = wc_get_product( $variation_id ? $variation_id : $product_id );
		if ( ! $product ) {
			return;
		}

		//Make sure stock is sufficient
		$quantity = $this->validate_free_product_quantity( $product, $quantity );
		$quantity = apply_filters( 'wjecf_set_free_product_amount_in_cart', $quantity, $product ); // 2.3.3

		$cart_item_key = $this->find_free_product_in_cart( $product_id, $variation_id, WC()->cart->get_cart() );

		// If cart_item_key is set, the item is already in the cart
		if ( $cart_item_key ) {
			$prev_quantity = WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
			if ( $quantity != $prev_quantity ) {
				WC()->cart->set_quantity( $cart_item_key, $quantity, false );
			}
		} else {
			$cart_item_data  = array( self::META_KEY_FREE_PRODUCT => true );
			$cart_item_key   = $this->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );
		}

		if ( $cart_item_key && isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) { //Might be removed by qty=0 therefore check with isset
			//Move free items to the end of the cart
			$temp = WC()->cart->cart_contents[ $cart_item_key ];
			unset( WC()->cart->cart_contents[ $cart_item_key ] );
			WC()->cart->cart_contents[ $cart_item_key ] = $temp;
		}

		return $cart_item_key;
	}

	/**
	 * Add a product to the cart.
	 *
	 * @param int $product_id contains the id of the product to add to the cart
	 * @param int $quantity contains the quantity of the item to add
	 * @param int $variation_id
	 * @param array $variation attribute values
	 * @param array $cart_item_data extra cart item data we want to pass into the item
	 * @return string|bool $cart_item_key
	 */
	private function add_to_cart( $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
		//TODO: Might WC_Cart::add_to_cart() cause issues at triggering woocommerce_add_to_cart (which triggers calculate_totals)?
		return WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );
	}

	private function find_free_product_in_cart( $product_id, $variation_id, $cart_contents ) {
		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == $product_id && $cart_item['variation_id'] == $variation_id && $this->is_free_cart_item( $cart_item ) ) {
				return $cart_item_key;
			}
		}
		return false;
	}


	/* INTERNAL */
	/**
	 * Get array of the free product ids.
	 * @param WC_Coupon $coupon_id The coupon id, code or a WC_Coupon object
	 * @return array Id's of the free products or an empty array.
	 */
	public function get_coupon_free_product_ids( $coupon ) {
		$v         = $coupon->get_meta( '_wjecf_free_product_ids' );
		$sanitized = WJECF()->sanitizer()->sanitize( $v, 'int[]' );

		//Translate (WPML)
		return apply_filters( 'wjecf_get_product_ids', $sanitized, 10, 1 );
	}

	/**
	 * Must a free product be selected?
	 * @param WC_Coupon $coupon The coupon code or a WC_Coupon object
	 * @return bool
	 */
	public function must_select_free_product( $coupon ) {
		return $coupon->get_meta( '_wjecf_must_select_free_product' ) == 'yes';
	}

	/**
	 * Get the 'select free gift'-message.
	 * @param WC_Coupon|string|int $coupon The coupon code or a WC_Coupon object
	 * @param string $context 'raw' or 'view'. If view is used, the translated value will be retrieved
	 * @return string|bool will be false if raw and empty.
	 */
	public function get_select_free_product_message( $coupon, $context = 'view' ) {
		$coupon  = WJECF_WC()->get_coupon( $coupon );
		$message = $coupon->get_meta( '_wjecf_select_free_product_message' );
		if ( ! $message ) {
			$message = 'Please choose your free gift:'; //Default
		}
		if ( 'raw' != $context ) {
			//Get translated value
			$message = __( $message, 'woocommerce-jos-autocoupon' ); // phpcs:ignore
			//$message = str_replace( '{amount}', $this->get_free_product_amount_for_coupon( $coupon ), $message );
		}

		return $message;
	}

	/**
	 * Get the 'select a free gift'-coupons that are currently in the cart
	 * @return WC_Coupon[]
	 */
	public function get_applied_select_free_product_coupons() {
		$coupons = array();
		foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
			$coupon = new WC_Coupon( $coupon_code );
			if ( $this->must_select_free_product( $coupon ) ) {
				$coupons[] = $coupon;
			}
		}
		return $coupons;
	}


	/**
	 * Checks if the coupon is a free-product or bogo coupon.
	 *
	 * @since 3.0.7
	 * @param WC_Coupon $coupon
	 * @return bool
	 */
	public function is_free_product_coupon( $coupon ) {
		if ( count( $this->get_coupon_free_product_ids( $coupon ) ) > 0 ) {
			return true;
		}
		if ( $coupon->get_meta( '_wjecf_bogo_matching_products' ) == 'yes' ) {
			return true;
		}
		return false;
	}

	/**
	 * Copies the selection from the $form_items array, but verifies that:
	 * - the product_ids are allowed free products
	 * - the total quantity does not exceed the free product amount for the coupon.
	 *
	 * Result is an array in this format: [ product_id => quantity ]
	 *
	 * @since 2.5.1
	 * @param WC_Coupon $coupon
	 * @param array $form_items [ product_id => WJECF_Free_Product_Form_Item, ... ]
	 * @return array [ product_id => quantity ]
	 */
	private function count_selected_products( $coupon, $form_items ) {

		$coupon_free_product_ids = $this->get_coupon_free_product_ids( $coupon );
		$qty_left                = $this->get_free_product_amount_for_coupon( $coupon );

		$verified_free_products = array();
		foreach ( $form_items as $form_item ) {
			if ( $qty_left <= 0 ) {
				break;
			}

			if ( ! $form_item instanceof WJECF_Free_Product_Form_Item ) {
				continue;
			}

			//Product is allowed for the coupon?
			if ( ! in_array( $form_item->getProductId(), $coupon_free_product_ids ) ) {
				continue;
			}

			//Valid ariation selected?
			$product_or_variation_id = $form_item->getProductOrVariationId();
			if ( ! $product_or_variation_id ) {
				continue;
			}

			//Maximum quantity of free products
			$quantity = $form_item->getQuantity();
			if ( WJECF_Free_Product_Form_Item::MAX_QUANTITY === $quantity ) {
				$quantity = $qty_left;
			}
			$quantity = min( $quantity, $qty_left ); //must not exceed the quantity we have left

			if ( $quantity <= 0 ) {
				continue;
			}

			//Yes, the product is allowed...

			$verified_free_products[ $product_or_variation_id ] = $quantity;
			$qty_left -= $quantity;
		}
		return $verified_free_products;
	}


	private $session_selected_products = null;
	/**
	 * API function
	 *
	 * Gets the selected product for the given coupon or false if nothing selected
	 * @param string $coupon_code
	 * @return WJECF_Free_Product_Form_Item[] Empty array if not found; otherwise an array of form items
	 */
	public function get_session_selected_products( $coupon_code ) {

		if ( is_null( $this->session_selected_products ) ) {
			$this->session_selected_products = WJECF()->get_session( 'selected_free_products', array() );
		}

		if ( ! isset( $this->session_selected_products[ $coupon_code ] ) ) {
			return array();
		}

		$selected_products = $this->session_selected_products[ $coupon_code ];
		//Legacy < 2.5.1; it's not an array, but just a $product_id
		if ( ! is_array( $selected_products ) ) {
			$free_prod_sel = new WJECF_Free_Product_Form_Item( $selected_products );
			$free_prod_sel->setQuantity( WJECF_Free_Product_Form_Item::MAX_QUANTITY );
			return array( $free_prod_sel );
		}
		//Convert the array to WJECF_Free_Product_Form_Item objects
		return $this->arrays_to_form_items( $selected_products );
	}

	/**
	 * Save Free product selection of the given coupon to the session
	 * @param string $coupon_code
	 * @param array $form_items Array of WJECF_Free_Product_Form_Item's
	 * @return void
	 */
	private function set_session_selected_products( $coupon_code, $form_items ) {
		if ( is_null( $this->session_selected_products ) ) {
			$this->session_selected_products = WJECF()->get_session( 'selected_free_products', array() );
		}
		//Convert WJECF_Free_Product_Form_Item objects to arrays
		$array_values = array();
		foreach ( $form_items as $form_item ) {
			$array_values[] = $form_item->toArray();
		}

		$this->session_selected_products[ $coupon_code ] = $array_values;
		WJECF()->set_session( 'selected_free_products', $this->session_selected_products );
	}

	private function clear_session_selected_products() {
		$this->session_selected_products = null;
		WJECF()->set_session( 'selected_free_products', $this->session_selected_products );
	}


	/**
	 * 1 if it's not a multiplying coupon, otherwise 1 or more
	 *
	 * @param WC_Coupon|string $coupon
	 * @return int
	 */
	private function get_product_multiplier_value( $coupon ) {
		if ( ! $this->allow_multiple_products( $coupon ) ) {
			return 1;
		}
		return WJECF()->get_coupon_multiplier_value( $coupon );
	}

	private function allow_multiple_products( $coupon ) {
		$coupon = WJECF_WC()->get_coupon( $coupon );
		return $coupon->get_meta( '_wjecf_multiply_free_products' ) === 'yes';
	}

	/**
	 * Is the cart item a free gift by means of a coupon?
	 * @param type $cart_item
	 * @return type
	 */
	private function is_free_cart_item( $cart_item ) {
		return isset( $cart_item[ self::META_KEY_FREE_PRODUCT ] );
	}

	private function tag_as_free_product( $product ) {
		$product->update_meta_data( self::META_KEY_FREE_PRODUCT, 'yes', true );
	}

	public function is_free_product( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return false;
		}
		$meta = $product->get_meta( self::META_KEY_FREE_PRODUCT );
		return 'yes' === $meta;
	}

	/**
	 * The amount of free products allowed for the coupon.
	 *
	 * @since 2.5.1
	 * @param WC_Coupon|string $coupon
	 * @return int
	 */
	private function get_free_product_amount_for_coupon( $coupon ) {
		$coupon_qty = $this->get_product_multiplier_value( $coupon );
		return apply_filters( 'wjecf_free_product_amount_for_coupon', $coupon_qty, $coupon ); // 2.3.3
	}
}
