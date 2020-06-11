<?php
/**
 * WooCommerce Extended Coupon Features Log
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-auto-added-coupons/debug/log.php
 *
 * @version     2.6.0
 */

defined( 'ABSPATH' ) or die();

?>
<style> 
	.soft79_wjecf_log { font-size:11px;} 
</style>
<table class='soft79_wjecf_log'>
	<tr>
		<th>Time</th>
		<th>Level</th>
		<th>Filter / Action</th>
		<th>Function</th>
		<th>Message</th>
	</tr>
<?php
foreach ( $log as $log_item ) {
	echo '<tr>';
	echo '<td>' . date( 'H:i:s', $log_item['time'] ) . '</td>';
	echo '<td>' . esc_html( $log_item['level'] ) . '</td>';
	echo '<td>' . esc_html( $log_item['filter'] ) . '</td>';
	echo '<td>' . esc_html( $log_item['class'] . '::' . $log_item['function'] ) . '</td>';
	echo '<td>' . esc_html( $log_item['message'] ) . '</td>';
	echo "</tr>\n";
}
?>
</table>
