<?php

namespace ACA\WC;

use AC;
use AC\Registrable;
use ACA\WC\ListScreen\Product;
use ACA\WC\ListScreen\ShopOrder;
use ACA\WC\Settings\HideOnScreen\FilterOrderCustomer;
use ACA\WC\Settings\HideOnScreen\FilterProductCategory;
use ACA\WC\Settings\HideOnScreen\FilterProductStockStatus;
use ACA\WC\Settings\HideOnScreen\FilterProductType;
use ACP\Settings\ListScreen\HideOnScreen\FilterPostDate;
use ACP\Settings\ListScreen\HideOnScreenCollection;

class Admin implements Registrable {

	public function register() {
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {

		switch ( true ) {
			case $list_screen instanceof ShopOrder :
				$collection->add( new FilterOrderCustomer(), 34 );
				break;

			case $list_screen instanceof Product :
				$collection->add( new FilterProductCategory(), 32 )
				           ->add( new FilterProductStockStatus(), 32 )
				           ->add( new FilterProductType(), 32 );

				$collection->remove( new FilterPostDate() );

				break;
		}
	}

}