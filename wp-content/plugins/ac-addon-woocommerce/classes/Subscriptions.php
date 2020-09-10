<?php

namespace ACA\WC;

use AC;
use ACA\WC\ListScreen\Product;
use ReflectionException;
use WC_Subscriptions;

/**
 * @since 3.4
 */
final class Subscriptions implements AC\Registrable {

	/**
	 * @return bool
	 */
	private function is_wc_subscriptions_active() {
		if ( ! class_exists( 'WC_Subscriptions', false ) ) {
			return false;
		}

		return version_compare( WC_Subscriptions::$version, '2.6', '>=' );
	}

	public function register() {
		if ( ! $this->is_wc_subscriptions_active() ) {
			return;
		}

		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
	}

	public function register_list_screens( AC\ListScreens $list_screens ) {
		$list_screens->register_list_screen( new ListScreen\Subscriptions() );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_groups( $groups ) {
		$groups->register_group( 'woocommerce_subscriptions', __( 'WooCommerce Subscriptions', 'codepress-admin-columns' ), 15 );
	}

	/**
	 * @throws ReflectionException
	 */
	public function add_columns( AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof Product ) {
			$list_screen->register_column_types_from_dir( 'ACA\WC\Column\ProductSubscription' );
		}
		if ( $list_screen instanceof AC\ListScreen\User ) {
			$list_screen->register_column_types_from_dir( 'ACA\WC\Column\UserSubscription' );
		}
	}

}