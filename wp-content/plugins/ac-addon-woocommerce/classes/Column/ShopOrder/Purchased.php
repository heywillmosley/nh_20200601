<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting\ShopOrder\ItemCount;
use ACP;
use WC_Order;
use WC_Order_Item_Product;

/**
 * @since 2.0
 */
class Purchased extends AC\Column
	implements AC\Column\AjaxValue, ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-purchased' )
		     ->set_label( __( 'Purchased', 'codepress-admin-columns' ) );
	}

	/**
	 * @param int $id
	 *
	 * @return bool|string
	 */
	public function get_value( $id ) {
		$count = $this->get_item_count( $id );

		if ( $count <= 0 ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%d item', '%d items', $count, 'codepress-admin-columns' ), $count );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $count, $this->get_name() );
	}

	/**
	 * @param int $order_id
	 *
	 * @return int
	 */
	private function get_item_count( $order_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
                SELECT SUM( oim.meta_value )
                FROM {$wpdb->prefix}woocommerce_order_items AS oi
                  INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
                WHERE oi.order_item_type = 'line_item'
                  AND oim.meta_key = '_qty'
                  AND oi.order_id = %d;
                  ", $order_id );

		return absint( $wpdb->get_var( $sql ) );
	}

	/**
	 * @param int $order_id
	 */
	public function get_ajax_value( $order_id ) {
		echo $this->get_order_items_html( wc_get_order( $order_id ) );
		exit;
	}

	public function get_raw_value( $id ) {
		return $this->get_item_count( $id );
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return string HTML
	 */
	private function get_order_items_html( $order ) {
		if ( ! $order ) {
			return false;
		}

		$order_items = $order->get_items();

		if ( count( $order_items ) <= 0 ) {
			return false;
		}

		ob_start();

		?>
		<table class="ac-table-items">
			<?php foreach ( $order_items as $item ) : ?>

				<?php if ( ! $item instanceof WC_Order_Item_Product ) {
					continue;
				} ?>

				<tr title="<?= $item->get_name(); ?>">
					<td class="ac-table-item-qty">
						<?php echo $item->get_quantity(); ?>x
					</td>
					<td class="ac-table-item-name">
						<div class="ac-table-item-name__wrap">
						<?php
						$product = $item->get_product();

						if ( $product ) {
							$properties = [];

							if ( wc_product_sku_enabled() && $product->get_sku() ) {
								$properties[] = $product->get_sku();
							}

							$properties[] = ac_helper()->html->link( get_edit_post_link( $product->get_id() ), $item->get_name() );

							echo implode( ' - ', $properties );
						} else {
							echo $item->get_name();
						}

						$item_meta = new WC_Order_Item_Product( $item );

						$item_meta_items = $item_meta->get_formatted_meta_data( true );

						if ( $item_meta_items ) {
							$meta_list = [];

							foreach ( $item_meta_items as $item_meta_item ) {
								$meta_list[] = sprintf( '%s: %s', $item_meta_item->display_key, $item_meta_item->value );
							}

							$meta_list_html = implode( '<br>', $meta_list );
							echo wc_help_tip( $meta_list_html );
						}
						?>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php

		return ob_get_clean();
	}

	public function sorting() {
		return new ItemCount();
	}

	public function search() {
		return new Search\ShopOrder\ProductCount();
	}
}