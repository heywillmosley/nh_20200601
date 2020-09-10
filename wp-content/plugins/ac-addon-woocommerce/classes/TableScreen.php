<?php

namespace ACA\WC;

use AC;
use ACA\WC\Settings\HideOnScreen\FilterOrderCustomer;
use ACA\WC\Settings\HideOnScreen\FilterProductCategory;
use ACA\WC\Settings\HideOnScreen\FilterProductStockStatus;
use ACA\WC\Settings\HideOnScreen\FilterProductType;
use ACA\WC\TableScreen\HideProductFilter;
use WC_Admin_List_Table_Orders;
use WP_Post;

/**
 * @since 3.0
 */
final class TableScreen {

	/**
	 * @var AC\ListScreen $list_screen
	 */
	private $current_list_screen;

	public function __construct() {
		add_action( 'ac/table/list_screen', [ $this, 'set_current_list_screen' ] );
		add_action( 'ac/table_scripts', [ $this, 'table_scripts' ] );
		add_action( 'ac/table_scripts/editing', [ $this, 'table_scripts_editing' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'product_scripts' ], 100 );
		add_action( 'admin_head', [ $this, 'display_width_styles' ], 10, 1 );
		add_filter( 'ac/editing/role_group', [ $this, 'set_editing_role_group' ], 10, 2 );

		// Add quick action to product overview
		if ( ac_addon_wc()->is_wc_version_gte( '3.3' ) ) {
			add_filter( 'post_row_actions', [ $this, 'add_quick_action_variation' ], 10, 2 );
			add_action( 'manage_product_posts_custom_column', [ $this, 'add_quick_link_variation' ], 11, 2 );
		}

		add_action( 'ac/table_scripts', [ $this, 'hide_filters' ] );
	}

	public function hide_filters( AC\ListScreen $list_screen ) {
		global $wc_list_table;

		if ( $wc_list_table instanceof WC_Admin_List_Table_Orders && ( new FilterOrderCustomer() )->is_hidden( $list_screen ) ) {
			remove_action( 'restrict_manage_posts', [ $wc_list_table, 'restrict_manage_posts' ] );
		}

		$services = [
			new HideProductFilter( $list_screen, new FilterProductType(), 'product_type' ),
			new HideProductFilter( $list_screen, new FilterProductCategory(), 'product_category' ),
			new HideProductFilter( $list_screen, new FilterProductStockStatus(), 'stock_status' ),
		];

		foreach ( $services as $service ) {
			$service->register();
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function set_current_list_screen( $list_screen ) {
		if ( ! $list_screen ) {
			return;
		}

		$this->current_list_screen = $list_screen;
	}

	public function get_current_list_screen() {
		return $this->current_list_screen;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function is_wc_list_screen( $list_screen ) {
		return $list_screen instanceof ListScreen\ShopOrder ||
		       $list_screen instanceof ListScreen\ShopCoupon ||
		       $list_screen instanceof ListScreen\Product ||
		       $list_screen instanceof ListScreen\ProductVariation ||
		       $list_screen instanceof AC\ListScreen\User;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function table_scripts_editing( $list_screen ) {
		if ( ! $this->is_wc_list_screen( $list_screen ) ) {
			return;
		}

		// Translations
		wp_localize_script( 'acp-editing-table', 'acp_woocommerce_i18n', [
			'woocommerce' => [
				'stock_qty'                 => __( 'Stock Qty', 'woocommerce' ),
				'manage_stock'              => __( 'Manage stock', 'woocommerce' ),
				'stock_status'              => __( 'Stock status', 'woocommerce' ),
				'in_stock'                  => __( 'In stock', 'woocommerce' ),
				'out_of_stock'              => __( 'Out of stock', 'woocommerce' ),
				'backorder'                 => __( 'On backorder', 'woocommerce' ),
				'clear_sale_price'          => __( 'Clear Sale Price', 'codepress-admin-columns' ),
				'replace'                   => __( 'Replace', 'codepress-admin-columns' ),
				'increase_by'               => __( 'Increase by', 'codepress-admin-columns' ),
				'decrease_by'               => __( 'Decrease by', 'codepress-admin-columns' ),
				'set_new'                   => __( 'Set New', 'codepress-admin-columns' ),
				'regular'                   => __( 'Regular', 'codepress-admin-columns' ),
				'regular_price'             => __( 'Regular Price', 'codepress-admin-columns' ),
				'sale'                      => __( 'Sale', 'woocommerce' ),
				'sale_price'                => __( 'Sale Price', 'woocommerce' ),
				'sale_from'                 => __( 'Sale from', 'codepress-admin-columns' ),
				'sale_to'                   => __( 'Sale To', 'codepress-admin-columns' ),
				'set_sale_based_on_regular' => __( 'Set the sale price based on the regular price', 'codepress-admin-columns' ),
				'schedule'                  => __( 'Schedule', 'woocommerce' ),
				'scheduled'                 => __( 'Scheduled', 'codepress-admin-columns' ),
				'usage_limit_per_coupon'    => __( 'Usage limit per coupon', 'woocommerce' ),
				'usage_limit_per_user'      => __( 'Usage limit per user', 'woocommerce' ),
				'usage_limit_products'      => __( 'Usage limit products', 'woocommerce' ),
				'length'                    => __( 'Length', 'woocommerce' ),
				'width'                     => __( 'Width', 'woocommerce' ),
				'height'                    => __( 'Height', 'woocommerce' ),
				'rounding_none'             => __( 'No Rounding', 'codepress-admin-columns' ),
				'rounding_up'               => __( 'Round up', 'codepress-admin-columns' ),
				'rounding_down'             => __( 'Round down', 'codepress-admin-columns' ),
				'rounding_example'          => __( 'Example:', 'codepress-admin-columns' ),
				'downloadable'              => __( 'Downloadable', 'woocommerce' ),
				'virtual'                   => __( 'Virtual', 'woocommerce' ),
			],
		] );

		if ( $list_screen instanceof ListScreen\ProductVariation ) {

			wp_localize_script( 'acp-editing-table', 'woocommerce_admin_meta_boxes', [
				'calendar_image'         => WC()->plugin_url() . '/assets/images/calendar.png',
				'currency_format_symbol' => get_woocommerce_currency_symbol(),
			] );
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @since 1.3
	 */
	public function table_scripts( $list_screen ) {
		if ( ! $this->is_wc_list_screen( $list_screen ) ) {
			return;
		}

		wp_enqueue_style( 'aca-wc-column', ac_addon_wc()->get_url() . 'assets/css/table.css', [], ac_addon_wc()->get_version() );
		wp_enqueue_script( 'aca-wc-table', ac_addon_wc()->get_url() . 'assets/js/table.js', [ 'jquery' ], ac_addon_wc()->get_version() );

		wp_localize_script( 'aca-wc-table', 'acp_wc_table', [
			'edit_post_link' => add_query_arg( [ 'action' => 'edit' ], admin_url() . 'post.php' ),
		] );
	}

	/**
	 * Single product scripts
	 *
	 * @param string $hook
	 */
	public function product_scripts( $hook ) {
		global $post;

		if ( in_array( $hook, [ 'post-new.php', 'post.php' ] ) && $post && 'product' === $post->post_type ) {
			wp_enqueue_script( 'aca-wc-product', ac_addon_wc()->get_url() . 'assets/js/product.js', [ 'jquery' ], ac_addon_wc()->get_version() );
		}
	}

	/**
	 * @param string $group
	 * @param string $role
	 *
	 * @return string
	 */
	public function set_editing_role_group( $group, $role ) {
		if ( in_array( $role, [ 'customer', 'shop_manager' ] ) ) {
			$group = __( 'WooCommerce', 'codepress-admin-columns' );
		}

		return $group;
	}

	/**
	 * @param int $product_id
	 *
	 * @return string
	 */
	private function get_list_table_link( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product || 'variable' !== $product->get_type() ) {
			return false;
		}

		return add_query_arg( [ 'post_type' => 'product_variation', 'post_parent' => $product_id ], admin_url( 'edit.php' ) );
	}

	/**
	 * Add a quick action on the product overview which links to the product variations page.
	 *
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public function add_quick_action_variation( $actions, $post ) {
		if ( 'product' !== $post->post_type ) {
			return $actions;
		}

		$link = $this->get_list_table_link( $post->ID );

		if ( $link ) {
			$actions['variation'] = ac_helper()->html->link( $link, __( 'View Variations', 'codepress-admin-columns' ) );
		}

		return $actions;
	}

	/**
	 * Display an icon on the product name column which links to the product variations page.
	 *
	 * @param string $column
	 * @param int    $post_id
	 *
	 * @see \WP_Posts_List_Table::column_default
	 */
	public function add_quick_link_variation( $column, $post_id ) {
		if ( 'name' !== $column ) {
			return;
		}

		$link = $this->get_list_table_link( $post_id );

		if ( ! $link ) {
			return;
		}

		$label = ac_helper()->html->tooltip( '<span class="ac-wc-view"></span>', __( 'View Variations', 'codepress-admin-columns' ) );

		echo ac_helper()->html->link( $link, $label, [ 'class' => 'view-variations' ] );
	}

	/**
	 * Applies the width setting to the table headers
	 */
	public function display_width_styles() {
		if ( ! $this->get_current_list_screen() instanceof ListScreen\ShopOrder ) {
			return;
		}

		$css_column_width = '';

		foreach ( $this->current_list_screen->get_columns() as $column ) {
			/* @var AC\Settings\Column\Width $setting */
			$setting = $column->get_setting( 'width' );

			$width = $setting->get_display_width();

			if ( ! $width ) {
				$width = 'auto';
			}

			$css_column_width .= '.ac-' . esc_attr( $this->current_list_screen->get_key() ) . ' .wrap table th.column-' . esc_attr( $column->get_name() ) . ' { width: ' . $width . ' !important; }';
			$css_column_width .= 'body.acp-overflow-table.ac-' . esc_attr( $this->current_list_screen->get_key() ) . ' .wrap th.column-' . esc_attr( $column->get_name() ) . ' { min-width: ' . $width . ' !important; }';

		}

		if ( ! $css_column_width ) {
			return;
		}

		?>
		<style>
			@media screen and (min-width: 783px) {
			<?php echo $css_column_width; ?>
			}
		</style>

		<?php
	}

}