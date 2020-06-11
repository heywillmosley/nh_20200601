<?php

defined( 'ABSPATH' ) or die();

class WJECF_Pro_Free_Products_Admin {

	private $plugin = null;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/* ADMIN HOOKS */
	public function init_admin_hook() {
		//Inject columns
		if ( WJECF()->is_pro() ) {
			WJECF()->inject_coupon_column(
				'_wjecf_free_products',
				__( 'Free products', 'woocommerce-jos-autocoupon' ),
				array( $this, 'admin_render_shop_coupon_columns' ),
				'products'
			);
		}

		//Add the tab to the coupon edit page
		add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'filter_woocommerce_coupon_data_tabs' ), 10, 1 );
		add_action( 'woocommerce_coupon_data_panels', array( $this, 'action_woocommerce_coupon_data_panels' ), 10, 0 );
		//Metabox
		add_action( 'wjecf_coupon_metabox_products', array( $this, 'admin_coupon_metabox_products' ), 15, 2 );
	}

	//Add tabs to the coupon option page
	public function filter_woocommerce_coupon_data_tabs( $tabs ) {

		$tabs['extended_features_products'] = array(
			'label'  => __( 'Free products', 'woocommerce-jos-autocoupon' ),
			'target' => 'wjecf_coupondata_free_products',
			'class'  => 'wjecf_coupondata_free_products',
		);

		return $tabs;
	}

	//Add panel to the coupon option page
	public function action_woocommerce_coupon_data_panels() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		?>
			<div id="wjecf_coupondata_free_products" class="panel woocommerce_options_panel">
				<?php
					//Feed the panel with options
					/**
					 * @since 2.5.0
					 * Deprecated wjecf_coupon_metabox_products
					 * Replaced by wjecf_coupon_metabox_free_products
					 * All other items from this tab are in "Usage restriction" now
					 */
					do_action( 'wjecf_coupon_metabox_products', $thepostid, $post );
					do_action( 'wjecf_coupon_metabox_free_products', $thepostid, $post );
					WJECF_Admin()->admin_coupon_data_footer();
				?>
			</div>
		<?php
	}



	public function admin_render_shop_coupon_columns( $column, $post ) {
		switch ( $column ) {
			case '_wjecf_free_products':
				$free_product_ids = $this->plugin->get_coupon_free_product_ids( WJECF_WC()->get_coupon( (int) $post->ID ) );
				echo esc_html( implode( ', ', $free_product_ids ) );
				break;
		}
	}

	public function admin_coupon_metabox_products( $thepostid, $post ) {

		//=============================
		//Title
		echo '<h3>' . esc_html( __( 'Free products', 'woocommerce-jos-autocoupon' ) ) . "</h3>\n";

		//=============================
		//Free product ids
		$free_product_ids = $this->plugin->get_coupon_free_product_ids( WJECF_WC()->get_coupon( (int) $thepostid ) );

		echo '<p class="form-field"><label>' . __( 'Free products', 'woocommerce' ) . '</label>';
		WJECF_Admin_Html::render_admin_product_selector( 'wjecf_free_product_ids', '_wjecf_free_product_ids', $free_product_ids, null );
		echo WJECF_Admin_Html::wc_help_tip( __( 'Free products that will be added to the cart when this coupon is applied.', 'woocommerce-jos-autocoupon' ) );
		echo '</p>';

		//=============================
		//2.3.0 Select free product
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wjecf_must_select_free_product',
				'label'       => __( 'Customer must select', 'woocommerce-jos-autocoupon' ),
				'description' => __( 'Check this box if the customer must choose from the free products.', 'woocommerce-jos-autocoupon' ),
			)
		);

		//=============================
		//2.3.0 Select free product
		$message = $this->plugin->get_select_free_product_message( $thepostid, 'raw' );
		woocommerce_wp_text_input(
			array(
				'id'          => '_wjecf_select_free_product_message',
				'label'       => __( '\'Select your gift\'-message', 'woocommerce-jos-autocoupon' ),
				'placeholder' => __( 'Please choose your free gift:', 'woocommerce-jos-autocoupon' ),
				'description' => __( 'This message is displayed when the customer must choose a free product.', 'woocommerce-jos-autocoupon' ),
				'desc_tip'    => true,
				'value'       => $message,
			)
		);

		//=============================
		//2.2.2 Allow multiplying the free products
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wjecf_multiply_free_products',
				'label'       => __( 'Allow multiplication of the free products', 'woocommerce-jos-autocoupon' ),
				'description' => // '<b>' . __( 'EXPERIMENTAL: ', 'woocommerce-jos-autocoupon' ) . '</b>' .
				__( 'The amount of free products is multiplied every time the minimum spend, subtotal or quantity is reached.', 'woocommerce-jos-autocoupon' ),
			)
		);

		//=============================
		//2.2.5 BOGO All matching products
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wjecf_bogo_matching_products',
				'label'       => __( 'BOGO matching products', 'woocommerce-jos-autocoupon' ),
				'description' => // '<b>' . __( 'EXPERIMENTAL: ', 'woocommerce-jos-autocoupon' ) . '</b>' .
				__( 'Buy one or more of any of the matching products (see \'Usage Restriction\'-tab) and get one free. Check \'Allow multiplication\' to get one free item for every matching item in the cart.', 'woocommerce-jos-autocoupon' ),
			)
		);
	}

	public function admin_coupon_meta_fields( $coupon ) {
		return array(
			'_wjecf_free_product_ids'            => 'int,',
			'_wjecf_multiply_free_products'      => 'yesno',
			'_wjecf_bogo_matching_products'      => 'yesno',
			'_wjecf_must_select_free_product'    => 'yesno',
			'_wjecf_select_free_product_message' => 'clean',
		);
	}
}
