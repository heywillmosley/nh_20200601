<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;
use WP_Roles;

/**
 * @since 2.0
 */
class Customer extends AC\Column\Meta
	implements ACP\Export\Exportable, ACP\Filtering\Filterable, ACP\Search\Searchable, ACP\Sorting\Sortable {

	public function __construct() {
		$this->set_label( 'Customer' );
		$this->set_type( 'column-wc-order_customer' );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_customer_user';
	}

	public function register_settings() {
		$this->add_setting( new Settings\ShopOrder\Customer( $this ) );
	}

	/**
	 * @return ACP\Export\Model
	 */
	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	/**
	 * @return ACP\Filtering\Model
	 */
	public function filtering() {

		switch ( $this->get_user_property() ) {
			case 'roles':

				return new Filtering\ShopOrder\CustomerRole( $this );
			default:

				return new ACP\Filtering\Model\Disabled( $this );
		}

	}

	public function search() {
		switch ( $this->get_user_property() ) {
			case 'roles':
				return new Search\ShopOrder\Customer\Meta\Serialized\Role( $this->get_roles() );

			case 'custom_field':
				if ( $this->get_related_meta_key() ) {
					return new Search\ShopOrder\Customer\Meta( $this->get_related_meta_key() );
				}
				break;
		}

		return new Search\ShopOrder\Customer();
	}

	public function sorting() {
		$setting = $this->get_setting( AC\Settings\Column\User::NAME );

		return ( new ACP\Sorting\Model\Post\MetaRelatedUserFactory() )->create( $setting->get_value(), $this->get_meta_key() );
	}

	/**
	 * @return string
	 */
	public function get_user_property() {
		$setting = $this->get_setting( 'user' );

		if ( ! $setting instanceof Settings\ShopOrder\Customer ) {
			return false;
		}

		return $setting->get_display_author_as();
	}

	private function get_roles() {
		$options = [];
		$roles = new WP_Roles();

		foreach ( $roles->roles as $key => $role ) {
			$options[ $key ] = translate_user_role( $role['name'] );
		}

		return $options;
	}

	public function get_related_meta_key() {
		if ( 'custom_field' === $this->get_user_property() ) {
			/** @var ACP\Settings\Column\UserCustomField $setting */
			$setting = $this->get_setting( 'custom_field' );

			return $setting->get_field();
		}

		return false;
	}

}