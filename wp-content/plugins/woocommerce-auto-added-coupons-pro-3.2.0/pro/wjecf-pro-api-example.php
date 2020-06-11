<?php
// phpcs:disable WordPress.NamingConventions.ValidFunctionName

add_action( 'wp_footer', 'WJECF_API_Test', 5, 0 );


/**
 * Example for WJECF_API() usage
 * @return void
 */
function WJECF_API_Test() {
	$all = WJECF_API()->get_all_auto_coupons();

	foreach ( $all as $code => $coupon ) {
		$values = WJECF_API_Test_Coupon( $coupon );
		echo '<h3>' . $code . '</h3>';
		echo '<ul>';
		foreach ( $values as $key => $value ) {
			printf( '<li><em>%s: </em> %s</li>', $key, print_r( $value, true ) );
		}
		echo '</ul>';
	}
}

function WJECF_API_Test_Coupon( $coupon ) {
	return array(
		'code'                               => WJECF_API()->get_coupon_code( $coupon ),
		'get_quantity_of_matching_products'  => WJECF_API()->get_quantity_of_matching_products( $coupon ),
		'get_subtotal_of_matching_products'  => WJECF_API()->get_subtotal_of_matching_products( $coupon ),
		'get_coupon_shipping_method_ids'     => WJECF_API()->get_coupon_shipping_method_ids( $coupon ),
		'get_coupon_payment_method_ids'      => WJECF_API()->get_coupon_payment_method_ids( $coupon ),
		'get_coupon_customer_ids'            => WJECF_API()->get_coupon_customer_ids( $coupon ),
		'get_coupon_customer_roles'          => WJECF_API()->get_coupon_customer_roles( $coupon ),
		'get_coupon_excluded_customer_roles' => WJECF_API()->get_coupon_excluded_customer_roles( $coupon ),
		'get_coupon_free_product_ids'        => WJECF_API()->get_coupon_free_product_ids( $coupon ),
	);
}
