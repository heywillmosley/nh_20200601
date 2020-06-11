<?php
/**
 * Select Free Product on Cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-auto-added-coupons/cart/select-free-product.php
 *
 * @version     2.6.0
 */

defined( 'ABSPATH' ) or die();

/**************************************************************************

Available variables:
	$free_gift_coupons     : (deprecated) An array of WC_Coupon objects applied to the cart that grant free product selections
	$template              : The template helper object (WJECF_Pro_Free_Products_Template)
	$coupons_form_data     : An array with the following info:
	[
		$coupon_code =>
			[
				'coupon'                  => The WC_Coupon object
				'coupon_code'             => The coupon code
				'allow_multiple_products' => True if multiplication is enabled for this coupon
				'form_items'              => WJECF_Free_Product_Item objects. Contains all info about the free products
				'selected_quantity'       => Amount of items selected by the customer
				'max_quantity'            => The max amount of free products for this coupon
				'name_prefix'             => The name prefix for all form input elements (checkbox / radiobutton / input type="number") for this coupon (e.g. 'wjecf_free_sel[0]')
				'id_prefix'               => The unique prefix to use for all DOM elements for this coupon ( e.g. 'wjecf_free_sel_0')
				'totalizer_id'            => The id of the <input> that is used to count the total amount of selected items (e.g. 'wjecf_free_sel_0_total_qty')
				'template'                => The template helper object (WJECF_Pro_Free_Products_Template)
			],
	]

**************************************************************************/

//Don't display if no free product selections...
if ( empty( $coupons_form_data ) ) {
	return;
}

?>
<tr class="wjecf-fragment-cart-select-free-product wjecf-auto-submit">
	<td colspan="6" data-title="<?php _e( 'Free products', 'woocommerce-jos-autocoupon' ); ?>">
		<?php

		foreach ( $coupons_form_data as $coupon_code => $coupon_form_data ) :
			$template->render_template( 'coupon-select-free-product.php', $coupon_form_data );
		endforeach;

		?>
	</td>
</tr>
