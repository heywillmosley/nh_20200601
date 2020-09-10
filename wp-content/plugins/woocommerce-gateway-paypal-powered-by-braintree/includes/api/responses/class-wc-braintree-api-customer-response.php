<?php
/**
 * WooCommerce Braintree Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@woocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Braintree Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Braintree Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/braintree/
 *
 * @package   WC-Braintree/Gateway/API/Responses/Customer
 * @author    WooCommerce
 * @copyright Copyright: (c) 2016-2020, Automattic, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Braintree API Customer Response Class
 *
 * Handles parsing customer responses
 *
 * @since 3.0.0
 */
class WC_Braintree_API_Customer_Response extends WC_Braintree_API_Vault_Response implements Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response, Framework\SV_WC_Payment_Gateway_API_Get_Tokenized_Payment_Methods_Response, Framework\SV_WC_Payment_Gateway_API_Customer_Response {


	/** @var \Braintree\CreditCard|\Braintree_PayPalAccount created payment method */
	protected $payment_method;


	/**
	 * Override the default constructor to set created payment method since
	 * Braintree simply provides a list of payment methods instead of an object
	 * containing the one just created ಠ_ಠ
	 *
	 * @since 3.0.0
	 * @param mixed $response response data from Braintree SDK
	 * @param string $response_type indicates whether the response is from a credit card or PayPal request
	 */
	public function __construct( $response, $response_type ) {

		parent::__construct( $response, $response_type );

		// set created payment method when creating customer
		if ( isset( $this->response->customer ) ) {
			$this->payment_method = $this->get_created_payment_method();
		}
	}


	/**
	 * Get the transaction ID, which is typically only present for create customer
	 * requests when verifying the associated credit card. PayPal
	 * requests (successful or unsuccessful) do not return a transaction ID
	 *
	 * @since 3.0.0
	 */
	public function get_transaction_id() {

		if ( ! $this->is_credit_card_response() || ! $this->payment_method ) {
			return null;
		}

		return $this->payment_method->verification->id;
	}


	/**
	 * Get the single payment token from creating a new customer with a payment
	 * method
	 *
	 * @link https://developers.braintreepayments.com/reference/response/customer/php
	 *
	 * @since 3.0.0
	 * @return \WC_Braintree_Payment_Method
	 */
	public function get_payment_token() {

		return new WC_Braintree_Payment_Method( $this->payment_method->token, $this->get_payment_token_data( $this->payment_method ) );
	}


	/**
	 * Get the payment tokens for the customer
	 *
	 * @link https://developers.braintreepayments.com/reference/response/customer/php
	 *
	 * @since 3.0.0
	 * @return array associative array of token => WC_Braintree_Payment_Method objects
	 */
	public function get_payment_tokens() {

		$tokens = array();

		foreach ( $this->response->paymentMethods() as $method ) {

			// only credit cards or PayPal accounts
			if ( ! in_array( get_class( $method ), array( 'Braintree\CreditCard', 'Braintree\PayPalAccount' ) ) ) {
				continue;
			}

			$tokens[ $method->token ] = new WC_Braintree_Payment_Method( $method->token, $this->get_payment_token_data( $method ) );
		}

		return $tokens;
	}


	/**
	 * Get the customer ID generated by Braintree when creating a new customer
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_customer_id() {

		return isset( $this->response->customer ) ? $this->response->customer->id : null;
	}


	/** Risk Data feature *****************************************************/


	/**
	 * Returns true if the transaction has risk data present. If this is not
	 * present, advanced fraud tools are not enabled (and set to "show") in
	 * the merchant's Braintree account and/or not enabled within plugin settings
	 *
	 * @since 3.0.0
	 */
	public function has_risk_data() {

		return isset( $this->payment_method->verification->riskData );
	}


	/**
	 * Get the risk ID for this transaction
	 *
	 * @since 3.0.0
	 */
	public function get_risk_id() {

		return $this->has_risk_data() ? $this->payment_method->verification->riskData->id : null;
	}


	/**
	 * Get the risk decision for this transaction, one of: 'not evaulated',
	 * 'approve', 'review', 'decline'
	 *
	 * @since 3.0.0
	 */
	public function get_risk_decision() {

		return $this->has_risk_data() ? $this->payment_method->verification->riskData->decision : null;
	}


	/** Helpers ***************************************************************/


	/**
	 * Helper to return the payment method created along with the customer, can be
	 * either a credit card or PayPal account
	 *
	 * @since 3.0.0
	 * @return \Braintree_CreditCard|\Braintree_PayPalAccount
	 */
	protected function get_created_payment_method() {

		if ( $this->is_credit_card_response() ) {
			return isset( $this->response->customer->creditCards ) && is_array( $this->response->customer->creditCards ) ? $this->response->customer->creditCards[0] : null;
		} else {
			return isset( $this->response->customer->paypalAccounts ) && is_array( $this->response->customer->creditCards ) ? $this->response->customer->paypalAccounts[0] : null;
		}
	}


}
