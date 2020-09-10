=== Braintree for WooCommerce Payment Gateway ===
Contributors: automattic, akeda, allendav, royho, slash1andy, woosteve, spraveenitpro, mikedmoore, fernashes, shellbeezy, danieldudzic, dsmithweb, fullysupportedphil, corsonr, zandyring, skyverge
Tags: ecommerce, e-commerce, commerce, woothemes, wordpress ecommerce, store, sales, sell, shop, shopping, cart, checkout, configurable, paypal, braintree
Requires at least: 4.4
Tested up to: 5.5
Requires PHP: 5.4
Stable tag: 2.4.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Accept PayPal, Credit Cards, and Debit Cards on your WooCommerce store.

== Description ==

The Braintree for WooCommerce gateway lets you accept **credit cards and PayPal payments** on your WooCommerce store via Braintree. Customers can save their credit card details or link a PayPal account to their WooCommerce user account for fast and easy checkout.

With this gateway, you can **securely sell your products** online using Hosted Fields, which help you meet security requirements without sacrificing flexibility or an integrated checkout process. Hosted Fields, similar to iFrames, are hosted on PayPal's servers but fit inside the checkout form elements on your site, providing a **secure, seamless** means for customers to share their payment information.

Braintree for WooCommerce supports tokenization, letting your customers save their credit cards or connect their PayPal account for faster, easier subsequent checkouts. The gateway also supports <a href="https://woocommerce.com/products/woocommerce-subscriptions/" target="_blank">WooCommerce Subscriptions</a> to let you sell products with recurring billing and <a href="https://woocommerce.com/products/woocommerce-pre-orders/" target="_blank">WooCommerce Pre-Orders</a>, which supports accepting payments for upcoming products as they ship or up-front.

= Powering Advanced Payments =

Braintree for WooCommerce provides several advanced features for transaction processing and payment method management.

- Meets [PCI Compliance SAQ-A](https://www.pcisecuritystandards.org/documents/Understanding_SAQs_PCI_DSS_v3.pdf) standards
- Supports [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/), and [WooCommerce Pre-Orders](https://woocommerce.com/products/woocommerce-pre-orders/)
- Customers can securely save credit cards or link PayPal accounts to your site
- Easily process refunds, void transactions, and capture charges right from WooCommerce
- Route payments in different currencies to different Braintree accounts (requires currency switcher)
- Supports Braintree's [extensive suite of fraud tools](https://articles.braintreepayments.com/guides/fraud-tools/overview)
- Supports 3D Secure
- Includes express checkout options like Buy Now buttons on product pages and PayPal Connect buttons in the Cart
- ...and much more!

== Installation ==

= Minimum Requirements =

- PHP 5.4+ (you can see this under <strong>WooCommerce &gt; Status</strong>)</li>
- WooCommerce 2.6+
- WordPress 4.4+
- An SSL certificate
- cURL support (most hosts have this enabled by default)

= Installation =

[Click here for instructions on installing plugins on your WordPress site.](https://wordpress.org/support/article/managing-plugins/#installing-plugins) We recommend using automatic installation as the simplest method.

= Updating =

Automatic updates should work like a charm, though we do recommend creating a backup of your site before updating, just in case.

If you do encounter an issue after updating, you may need to flush site permalinks by going to **Settings > Permalinks** and clicking **Save Changes**. That will usually return things to normal!

== Frequently Asked Questions ==

= Where can I find documentation? =

Great question! [Click here to review Braintree for WooCommerce documentation.](https://docs.woocommerce.com/document/woocommerce-gateway-paypal-powered-by-braintree/) This documentation includes detailed setup instructions and information about using the gateway's features.

= Does this plugin work with credit cards, or just PayPal? =

This plugin supports payments with credit cards and PayPal.

= Does this plugin support recurring payment, like for subscriptions? =

Yes! This plugin supports tokenization, which is required for recurring payments such as those created with [WooCommerce Subscriptions](http://woocommerce.com/products/woocommerce-subscriptions/).

= What currencies are supported? =

This plugin supports all countries in which Braintree is available. You can use your native currency, or you can add multiple merchant IDs to process different currencies via different Braintree accounts. To use multi-currency, your site must use a **currency switcher** to adjust the order currency (may require purchase). We’ve tested this plugin with the [Aelia Currency Switcher](https://aelia.co/shop/currency-switcher-woocommerce/) (requires purchase).

= Can non-US merchants use this plugin? =

Yes! This plugin supports all countries where Braintree is available.

= Does this plugin support testing and production modes? =

Yes! This plugin includes a production and sandbox mode so you can test without activating live payments.

= Credit cards are working fine, but PayPal's not working. What's going on? =

It sounds like you may need to enable PayPal on your Braintree account. [Click here for instructions on enabling PayPal in your Braintree control panel.](https://docs.woocommerce.com/document/woocommerce-gateway-paypal-powered-by-braintree/#section-6)

= Can I use this plugin just for PayPal? =

Sure thing! [Click here for instructions on setting up this gateway to only accept PayPal payments.](https://docs.woocommerce.com/document/woocommerce-gateway-paypal-powered-by-braintree#section-10)

= Will this plugin work with my site's theme? =

Braintree for WooCommerce should work nicely with any WooCommerce compatible theme (such as [Storefront](http://www.woocommerce.com/storefront/)), but may require some styling for a perfect fit. For assistance with theme customization, please visit the [WooCommerce Codex](https://docs.woocommerce.com/documentation/plugins/woocommerce/woocommerce-codex/).

= Where can I get support, request new features, or report bugs? =

First, please [check out our plugin documentation](https://docs.woocommerce.com/document/woocommerce-gateway-paypal-powered-by-braintree) to see if that addresses any of your questions or concerns.

If not, please get in touch with us through the [plugin forums](https://wordpress.org/support/plugin/woocommerce-gateway-paypal-powered-by-braintree/)!

== Screenshots ==

1. Enter Braintree credentials
2. Credit card gateway settings
3. Advanced credit card gateway settings
4. PayPal gateway settings
5. Checkout with PayPal directly from the cart
6. Checkout with PayPal directly from the product page

== Changelog ==

= 2020.08.12 - version 2.4.0 =
* Fix - Halt plugin loading and display a notice if cURL is not available on the server
* Misc - The plugin name is updated to Braintree for WooCommerce
* Misc - Add support for WooCommerce 4.3
* Misc - Update the SkyVerge plugin framework to v5.7.1
* Misc - Require PHP 5.6+
* Dev - Classes in the WC_Braintree\Plugin_Framework namespace are now deprecated, use the namespace for the included version of the SkyVerge plugin framework (SkyVerge\WooCommerce\PluginFramework\v5_7_1)

= 2020.06.03 - version 2.3.11 =
* Tweak - New users and users who disconnect should use API keys to connect to Braintree - no changes required for users already connected via Braintree Auth

= 2020.05.04 - version 2.3.10 =
* Misc - Add support for WooCommerce 4.1

= 2020.04.20 - version 2.3.9 =
* Fix - Fix fatal error in PHP < 5.4

= 2020.03.10 - version 2.3.8 =
* Misc - Add support for WooCommerce 4.0

= 2020.02.05 - version 2.3.7 =
* Fix - Prevent error with payment fields shown in the Checkout page on WooCommerce 3.9 when the address fields are updated

= 2020.01.05 - version 2.3.6 =
* Misc - Add support for WooCommerce 3.9

= 2019.12.12 - version 2.3.5 =
* Fix - Fix redirect URL encoding when connecting via Braintree Auth

= 2019.12.10 - version 2.3.4 =
* Tweak - Display informative message when 3D Secure fails due to unsupported characters in the customer name

= 2019.11.28 - version 2.3.3 =
* Fix - Catch CardinalCommerce JS errors in unsupported browsers to prevent hung checkouts

= 2019.10.21 - version 2.3.2 =
* Misc - Add support for WooCommerce 3.8

= 2019.10.16 - version 2.3.1 =
* Fix - Prevent 3D Secure errors trying to purchase free trial subscriptions
* Fix - Fix a bug with regular expressions being used in PHP 7.3+

= 2019.10.03 - version 2.3.0 =
* Feature - PayPal buy-now buttons can now be added to product pages
* Tweak - Enable PayPal Credit by default on new installs
* Fix - Fix a styling issue with the merchant account ID field in settings
* Fix - Fix a bug with a regular expression being used in PHP 7.3+

= 2019.09.12 - version 2.2.7 =
* Fix - Fix JavaScript error blocking payments with 3D Secure from the Pay Order page

= 2019.08.07 - version 2.2.6 =
* Tweak - Add support for 3D Secure 2.0
* Misc - Add support for WooCommerce 3.7

= 2019.06.06 - version 2.2.5 =
* Fix - Regenerate client tokens on checkout refresh to use the customer's latest currency
* Fix - Ensure saved PayPal accounts display their associated email address if no nickname is set

= 2019.04.01 - version 2.2.4 =
* Fix - Prevent an error when completing pre-orders that were placed using the PayPal gateway

= 2019.03.20 - version 2.2.3 =
* Fix - Ensure Kount merchant ID is set in device data for stores using advanced fraud tools via Kount

= 2019.02.28 - version 2.2.2 =
* Fix - Prevent JS errors when reloading the payment form in IE and Edge

= 2019.02.06 - version 2.2.1 =
* Fix - Ensure updated order totals are used for validating 3D Secure when the checkout is refreshed
* Fix - Prevent 3D Secure errors when non-US region codes are used during validation
* Fix - Ensure payment forms are available for orders that start at $0 but require payment after shipping selection
* Fix - Update the recurring flag for new API requirements when processing subscription payments
* Misc - Reorder manual connection setting inputs to match documentation

= 2018.11.12 - version 2.2.0 =
* Feature - Add Apple Pay support for iOS users to quickly place orders from the product, cart, and checkout pages
* Feature - Allow the PayPal button to be customized from the plugin settings
* Feature - Add PayPal Credit support
* Feature - Add support for auto-capturing orders when changed to a paid status
* Feature - Customers can now label their saved payment methods for easier identification when choosing how to pay
* Tweak - Improve the My Account Payment Methods table on desktop and mobile
* Tweak - Automatically enable 3D Secure when enabled in the merchant account
* Tweak - Allow users to set the card types that should process 3D Secure
* Tweak - Allow users to set the 3D Secure level and block transactions where liability is not shifted
* Fix - Fix an issue where duplicate addresses were added when processing transactions with a previously saved payment method
* Fix - Ensure the payment forms are re-created after shipping method selection
* Misc - Remove support for WooCommerce 2.5

= 2018.10.17 - version 2.1.4 =
* Misc - Add support for WooCommerce 3.5

= 2018.08.01 - version 2.1.3 =
* Tweak - Generalize the PayPal link error to allow for different PayPal button colors
* Fix - Ensure PayPal charges can still be captured when the Credit Card gateway is disabled
* Fix - Prevent stalled checkout when PayPal is cancelled or closed
* Fix - Prevent duplicate PayPal buttons when checkout is refreshed
* Fix - Don't reset the "Create Account" form when the checkout is refreshed

= 2.1.2 =
* Tweak - Add payment details to the customer data export and remove it for erasure requests
* Tweak - Remove payment tokens for customer data erasure requests
* Misc - Add support for WooCommerce 3.4

= 2.1.1 =
* Fix - Fix the payment form JavaScript compatibility with IE 11

= 2.1.0 =
* Feature - Upgrade to the latest Braintree JavaScript SDK for improved customer experience, reliability, and error handling
* Tweak - Add placeholder text for credit card inputs
* Tweak - Add responsive sizing to the PayPal buttons and update to the recommended styling for the Cart and Checkout pages
* Tweak - Add setting and filter to disable PayPal on the cart page
* Tweak - Update all translatable strings to the same text domain
* Tweak - Hide Kount as a fraud tool option for US-based stores as it's not currently supported
* Tweak - Only load the Braintree scripts when required on payment pages
* Fix - Ensure that new customers have their billing address stored in the vault on their first transaction
* Fix - Prevent linked PayPal accounts from being cleared if there are address errors at checkout
* Fix - Fix some deprecated function notices

= 2.0.4 =
* Fix - Prevent a fatal error when completing pre-orders
* Fix - Prevent JavaScript errors when applying a 100%-off coupon at checkout

= 2.0.3 =
* Fix - Add a missing namespace that could cause JavaScript issues with some configurations

= 2.0.2 =
* Fix - Ensure refunds succeed for legacy orders that are missing the necessary meta data
* Fix - Add fallbacks for certain subscriptions upgrades after WooCommerce 3.0 compatibility issues
* Fix - Default set the Require CSC setting for legacy upgrades to avoid inaccurate error notices at checkout
* Fix - Prevent PayPal JavaScript errors in certain cases
* Fix - Ensure subscriptions are not affected if Change Payment fails due to declines or other problems
* Fix - Ensure old payment methods can be removed by the customer after changing subscription payment to a new method

= 2.0.1 =
* Fix - Purchasing a subscription with PayPal could lead to a blank order note being added
* Fix - Ensure all upgrade routines run for users who have used both the SkyVerge Braintree and PayPal Powered by Braintree v1 in the past
* Fix - Issue where existing subscriptions in some cases couldn't switch to using a new PayPal account
* Fix - Ensure "Place Order" button always remains visible for PayPal when accepting terms

= 2.0.0 =
* Feature - Now supports non-USA Braintree merchant accounts! Bonjour, hola, hallo, and g'day :)
* Feature - Supports WooCommerce Pre-Orders plugin
* Feature - Credit cards and PayPal gateways can be enabled individually
* Feature - Customers can opt to save cards or link a PayPal account at checkout for future use, or use saved methods during checkout
* Feature - Customers can manage or add new payment methods from the account area
* Feature - Uses an enhanced payment form with retina icons
* Feature - Add multiple merchant IDs to support multi-currency shops (requires a currency switcher)
* Feature - Supports Advanced Fraud tools and Kount Direct
* Feature - Supports 3D Secure for Visa / MasterCard transactions
* Feature - Add dynamic descriptors to be displayed for the transaction on customer's credit card statements
* Feature - Can show detailed decline messages at checkout to better inform customers of transaction decline reasons
* Feature - Allows bulk action to capture charges
* Feature - Orders with only virtual items can now force a charge instead of authorization
* Tweak - Capturing a charge now moves order status to "processing" automatically
* Tweak - Voided orders are now marked as "cancelled" instead of "refunded"
* Tweak - Admins can now manually update Subscription payment methods and view payment tokens
* Fix - Subscription orders will no longer force a charge and allow an authorization depending on settings
* Fix - Handle Subscriptions renewal failures by failing the order
* Fix - Customers can switch Subscriptions payment methods on their own from the account
* Fix - Stores sandbox and live customer tokens separately to avoid `Customer ID is invalid.` messages
* Fix - Ensures that payment can be made from the "My Account" page for pending orders
* Misc - Adds support for WooCommerce 3.0+
* Misc - Removes support for WooCommerce 2.4 and lower
* Misc - Added upgrade routine from SkyVerge Braintree plugin to allow for migrating existing tokens and subscriptions
* Misc - Refactor for improved performance and stability
* Misc - Other small fixes and improvements

= 1.2.7 =
* Fix - If you connected but did not save the settings, the enabled value would not be set and scripts would not enqueue
* Fix - Disable customer initiated payment method changes - PayPal Braintree does not support zero amount transactions
* Tweak - On new installs, debug messages are no longer sent to the WooCommerce System Status log by default

= 1.2.6 =
* Fix - Issue where buyer unable to change subscription payment method with free-trial (order total is 0).

= 1.2.5 =
* Fix - Prevent void on unsettled transaction when refunding partially.
* Tweak - Add filter wc_gateway_paypal_braintree_sale_args to filter arguments passed to sale call.

= 1.2.4 =
* Fix - Free subscription trails not allowed.
* Fix - Subscription recurring billing after free trial not working.

= 1.2.3 =
* Fix - Handle uncaught exceptions thrown by Braintree SDK. API calls from SDK may throws exception, thus it need to be handled properly in try/catch block.
* Fix - Issue where deactivating WooCommerce might throws an error

= 1.2.2 =
* Tweak - Updated FAQ that emphasizes this plugin only works in the U.S. currently
* Fix - Updated JS SDK to 2.24.1 which should fixes issue where credit card fields working intermittently
* Tweak - Add filter on credit card icons
* Tweak - Provide default title for cards and PayPal account methods

= 1.2.1 =
* Fix - Issue where Subscriptions with free trial was not processed
* Fix - Missing "Change Payment" button in "My Subscriptions" section
* Tweak - Make enabled option default to 'yes'
* Tweak - Add adnmin notice to setup / connect after plugin is activated
* Fix - Consider more statuses (settling, submitted_for_settlement, settlement_pending) to mark order as in-processing
* Fix - Issue where settings section rendered twice

= 1.2.0 =
* Replace array initialization code that causes a fatal error on PHP 5.2 or earlier. PHP 5.4+ is still required, but this code prevented the compatibility check from running and displaying the version requirements
* Update to the latest Braintree SDK (3.8.0)
* Add authorize/capture feature, allowing delayed settlement
* Pre-fill certain merchant and store details when connecting
* Fix missing gateway title and transaction URL when order in-hold

= 1.1.0 =
* Fixed a bug which would cause the gateway settings to report that the gateway was enabled when it actually was not fully enabled.
* Updated contributors list

= 1.0.1 =
* Remove duplicate SSL warnings
* Update environment check to also check after activation for environment problems
* Fix link in enabled-but-not-connected notice

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.1.0 =
* Feature - Upgrade to the latest Braintree JavaScript SDK for improved customer experience, reliability, and error handling

= 2.0.4 =
* Fix - Prevent a fatal error when completing pre-orders
* Fix - Prevent JavaScript errors when applying a 100%-off coupon at checkout

= 1.2.4 =
* Fix - Free subscription trials not allowed.
* Fix - Subscription recurring billing after free trial not working.
