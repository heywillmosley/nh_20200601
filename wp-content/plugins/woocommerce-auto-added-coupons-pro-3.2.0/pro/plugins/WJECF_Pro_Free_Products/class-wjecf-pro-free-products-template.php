<?php

/**
 * Frontend / visualisation stuff
 *  - Templating
 *  - Appearance of free products in the cart
 */
class WJECF_Pro_Free_Products_Template {

	/**
	 * @var The WJECF_Pro_Free_Products instance
	 */
	private $plugin = null;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	public function init_hook() {
		//Frontend hooks - Cart visualisation
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'filter_woocommerce_cart_item_remove_link' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'filter_woocommerce_cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'filter_woocommerce_cart_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'filter_woocommerce_cart_item_quantity' ), 10, 2 );
	}

	/**
	 * Same as WordPress esc_attr() but also escapes [ and ] to prevent shortcodes from being run by certain plugins (I think YITH)
	 * @param string $attr String to escape
	 * @return string The escaped string
	 */
	public function esc_attr( $attr ) {
		$attr = esc_attr( $attr );
		$attr = strtr(
			$attr, array(
				'[' => '&#091;',
				']' => '&#093;',
			)
		); //prevents [products] shortcode to be run by certain plugins
		return $attr;
	}

	/**
	 * Renders the <input /> of a single item for the free product selection
	 *
	 * @since 2.6.0
	 * @param WJECF_Free_Product_Form_Item $form_item
	 * @param array $args Optional arguments e.g. [ 'class' => 'css-class', 'type' => 'checkbox', 'title' => 'This is the tooltip text'];
	 */
	public function render_form_item_input( $form_item, $args = array() ) {

		$product    = $form_item->getProduct();
		$value      = $form_item->getQuantity();
		$product_id = $form_item->getProductId();

		$input_type = isset( $args['type'] ) ? $args['type'] : 'radio';

		$class = isset( $args['class'] ) ? " class='" . esc_attr( $args['class'] ) . "'" : '';
		$title = isset( $args['title'] ) ? " title='" . esc_attr( $args['title'] ) . "'" : '';

		echo '<input type="hidden"'
		. ' name="' . $this->esc_attr( $form_item->field_name_prefix ) . '[product_id]" '
		. ' value="' . $form_item->getProductId() . '" />';

		switch ( $input_type ) {
			case 'radio':
				$checked = ( empty( $value ) ? '' : ' checked="checked"' );

				echo '<input type="radio" id="' . $form_item->field_id . '"'
				. ' name="' . $this->esc_attr( "{$form_item->name_prefix}[selected_product]" ) . '"'
				. ' value="' . $product_id . '"'
				. ' ' . $checked . $title
				. $class . ' />';
				return;

			case 'checkbox':
				echo '<input type="checkbox" id="' . $form_item->field_id . '"'
				. ' name="' . $this->esc_attr( "{$form_item->field_name_prefix}[quantity]" ) . '"'
				. ' value="1" ' . ( empty( $value ) ? '' : ' checked="checked"' )
				. ' data-wjecf-qty-totalizer="' . $form_item->totalizer_id . '"'
				. $class . $title . ' />';
				return;

			case 'number':
				echo '<input type="number"   id="' . $form_item->field_id . '"'
				. ' name="' . $this->esc_attr( "{$form_item->field_name_prefix}[quantity]" ) . '"'
				. ' value="' . intval( $value ) . '"'
				. ' min="0" ' //max="' . $max_quantity . '"'
				. ' data-wjecf-qty-totalizer="' . $form_item->totalizer_id . '"'
				. $class . $title . ' />';
				return;

			default:
				WJECF()->log( 'error', 'Unknown input_type: ' . $input_type );
		}
	}

	/**
	 * If the form_item is for a variable product renders the attribute selectors
	 * @since 2.6.0
	 * @param WJECF_Free_Product_Form_Item $form_item
	 */
	public function render_form_item_variations( $form_item ) {
		$product = $form_item->getProduct();
		if ( $product->is_type( 'variable' ) ) {
			$this->render_attribute_selectors(
				$product,
				$form_item->getAttributes(),
				$form_item->field_id,
				"{$form_item->field_name_prefix}[attributes]"
			);
		}
	}

	/**
	 * Renders the attribute selectors for the given product
	 * @param WC_Product $product The variable product
	 * @param array $selected_attributes Array with the selected attributes as [ attrib_name => value ]
	 * @param string $id_prefix prefix for the DOM-element id
	 * @param string $field_name_prefix prefix for the DOM-element name
	 */
	public function render_attribute_selectors( $product, $selected_attributes, $id_prefix, $field_name_prefix ) {
		//Variable product attributes
		$attributes           = $product->get_variation_attributes();
		$available_variations = $product->get_available_variations();

		//div to allow JS to identify the variation selectors. This div also contains available_variations to allow for JS to hide non-available variations.
		echo '<div class="variations" data-product_variations="' . htmlspecialchars( wp_json_encode( $available_variations ) ) . '">';
		foreach ( $attributes as $attribute_name => $options ) {
			$field_id            = $id_prefix . '_' . sanitize_title( $attribute_name );
			$sane_attribute_name = 'attribute_' . sanitize_title( $attribute_name );
			$selected            = isset( $selected_attributes[ $sane_attribute_name ] )
				? wc_clean( urldecode( $selected_attributes[ $sane_attribute_name ] ) )
				: $product->get_variation_default_attribute( $attribute_name );

			printf( '<label for="%s">%s</label>', $field_id, wc_attribute_label( $attribute_name ) );
			WJECF_WC()->wc_dropdown_variation_attribute_options(
				array(
					'id'        => $field_id,
					'name'      => $this->esc_attr( $field_name_prefix . '[' . $sane_attribute_name . ']' ),
					'options'   => $options,
					'attribute' => $attribute_name,
					'product'   => $product,
					'selected'  => $selected,
				)
			);
		}
		echo '</div>';
	}

	/**
	 * Calls WJECF()->include_template(), but will inject $this as $template
	 * @param string $template_name The PHP filename in the templates directory
	 * @param array $variables Array of variables that must be available in the template
	 */
	public function render_template( $template_name, $variables ) {
		WJECF()->include_template( $template_name, array_merge( $variables, array( 'template' => $this ) ) );
	}


	/**
	 * Notifies the customer that the amount of products in qtock is not sufficient.
	 * @param type $product
	 * @return type
	 */
	public function notify_not_enough_stock( $product ) {
		/* translators: 1: product title */
		$msg = __( 'Sorry, we do not have enough "%1$s" in stock (%2$s in stock). Please review your selection.', 'woocommerce-jos-autocoupon' );
		$msg = sprintf( $msg, $product->get_title(), $product->get_stock_quantity() );
		wc_add_notice( $msg, 'error' );
	}

	public function notify_select_variation( $product ) {
		/* translators: 1: product title */
		$msg = __( 'Please choose a variation of "%s".', 'woocommerce-jos-autocoupon' );
		$msg = sprintf( $msg, $product->get_title() );
		wc_add_notice( $msg, 'error' );
	}

	/**
	 * Show 'Free!' in the cart for free product
	 */
	public function filter_woocommerce_cart_item_price( $price_html, $cart_item, $cart_item_key ) {
		if ( $this->plugin->is_free_product( $cart_item['data'] ) ) {
			$price_html = apply_filters( 'wjecf_free_cart_item_price', __( 'Free!', 'woocommerce' ), $price_html, $cart_item, $cart_item_key );
		}
		return $price_html;
	}

	/**
	 * Show 'Free!' in the cart for free product
	 */
	public function filter_woocommerce_cart_item_subtotal( $price_html, $cart_item, $cart_item_key ) {
		if ( $this->plugin->is_free_product( $cart_item['data'] ) ) {
			$price_html = apply_filters( 'wjecf_free_cart_item_subtotal', __( 'Free!', 'woocommerce' ), $price_html, $cart_item, $cart_item_key );
		}
		return $price_html;
	}

	/**
	 * Quantity is readonly for free product
	 */
	public function filter_woocommerce_cart_item_quantity( $product_quantity_html, $cart_item_key ) {
		$cart_item = WJECF_WC()->get_cart_item( $cart_item_key );

		if ( $this->plugin->is_free_product( $cart_item['data'] ) ) {
			$qty                   = intval( $cart_item['quantity'] );
			$product_quantity_html = sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="%d" />', $qty, $cart_item_key, $qty );
		}
		return $product_quantity_html;
	}

	/**
	 * Remove the 'remove item'-link
	 */
	public function filter_woocommerce_cart_item_remove_link( $remove_html, $cart_item_key ) {
		$cart_contents = WC()->cart->get_cart();
		//Remove the link if it's a free item
		if ( $this->plugin->is_free_product( $cart_contents[ $cart_item_key ]['data'] ) ) {
			return '';
		}
		return $remove_html;
	}
}
