<?php /* phpcs:ignore */

if ( defined( 'ABSPATH' ) && ! class_exists( 'WJECF_Pro_Admin_Auto_Update' ) ) {

	/**
	 * Handles automatic update for WooCommerce Extended Coupon Features.
	 */
	class WJECF_Pro_Admin_Auto_Update extends Abstract_WJECF_Plugin {

		const AUTO_UPDATE_API_URL    = 'https://www.soft79.nl/';
		const AUTO_UPDATE_PRODUCT_ID = 'woocommerce-auto-added-coupons-pro';

		const DOM_PREFIX    = 'wjecf-';
		const SETTINGS_PAGE = 'wjecf_settings'; // In the wp_options table
		const OPTION_NAME   = 'wjecf_licence'; // In the wp_options table

		//DONT STORE THIS ARRAY AS A CONSTANT (NOT SUPPORTED IN OLDER PHP VERSIONS)
		protected $statuses_must_activate = array( 's203', 's204', 'e204' ); //In these statuses, the licence must be activated

		protected $options = null; // WJECF_Options object

		public function __construct() {
			$this->set_plugin_data(
				array(
					'description'     => __( 'Automatically upgrade this plugin when a new version is available.', 'woocommerce-jos-autocoupon' ),
					'dependencies'    => array(),
					'can_be_disabled' => true,
				)
			);

			$this->options = new WJECF_Options(
				self::OPTION_NAME, array(
					'licence_key'           => '', // e.g. '12345-6789'
					'domain'                => '',
					'licence_valid'         => false,
					'licence_valid_message' => '',
				), true
			);
		}

		public function init_admin_hook() {
			$this->init_update_api();
			$this->maybe_display_licence_notice();

			add_action( 'wjecf_admin_before_settings', array( $this, 'wjecf_admin_before_settings' ), 100 );
		}

		public function wjecf_admin_before_settings() {
			$page = self::SETTINGS_PAGE;

			register_setting( self::SETTINGS_PAGE, self::OPTION_NAME, array( $this, 'validate_settings' ) );

			// Section LICENCE
			add_settings_section(
				self::DOM_PREFIX . 'section_licence',
				__( 'Licence key', 'woocommerce-jos-autocoupon' ),
				array( $this, 'render_section' ),
				$page
			);

			add_settings_field(
				self::DOM_PREFIX . 'domain',
				__( 'Domain', 'woocommerce-jos-autocoupon' ),
				array( $this, 'render_setting_domain' ),
				$page,
				self::DOM_PREFIX . 'section_licence'
			);

			add_settings_field(
				self::DOM_PREFIX . 'licence_key',
				__( 'Licence key', 'woocommerce-jos-autocoupon' ),
				array( $this, 'render_setting_licence_key' ),
				$page,
				self::DOM_PREFIX . 'section_licence'
			);
		}

		/**
		 * Display licence notice if applicable.
		 *
		 * @return void
		 */
		private function maybe_display_licence_notice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$valid = $this->options->get( 'licence_valid', false );
			if ( $valid ) {
				return;
			}

			$wjecf_admin = WJECF()->get_plugin( 'admin' );
			if ( $wjecf_admin ) {
				$url     = admin_url( 'options-general.php?page=wjecf_settings' );
				$message = $this->options->get( 'licence_valid_message', __( 'Licence key is not valid.', 'woocommerce-jos-autocoupon' ) );
				$message = $message . ' ' . sprintf(
					// translators: 1:url
					__( 'You will not receive automatic updates. Please enter a valid licence key <a href="%s">here</a>.', 'woocommerce-jos-autocoupon' ),
					esc_attr( $url )
				);
				$wjecf_admin->enqueue_notice( $message, 'notice-warning' );
			}
			add_action( 'wjecf_coupon_metabox_misc', array( $this, 'wjecf_coupon_metabox_misc' ), 20, 2 );
		}

		public function render_section( $section ) {
			switch ( $section['id'] ) {
				case self::DOM_PREFIX . 'section_licence':
					$soft79 = '<a href="http://www.soft79.nl" target="_blank">soft79.nl</a>';
					$body   = __( 'A valid licence key will allow you to update the plugin from the WordPress admin area.', 'woocommerce-jos-autocoupon' ) . ' ';
					// translators: 1:url
					$body .= sprintf( __( 'You can manage your licences at your account page at %s.', 'woocommerce-jos-autocoupon' ), $soft79 ) . ' ';
					$body .= __( 'This option is not available if you purchased from Envato market.', 'woocommerce-jos-autocoupon' );
					printf( '<p>%s</p>', $body );
					break;
			}
		}

		public function render_setting_licence_key() {
			$option_name = 'licence_key';
			WJECF_Admin_Html::render_input(
				array(
					'type'  => 'text',
					'id'    => self::DOM_PREFIX . $option_name,
					'class' => 'regular-text',
					'name'  => sprintf( '%s[%s]', self::OPTION_NAME, $option_name ),
					'value' => $this->options->get( $option_name ),
				)
			);

			$valid   = $this->options->get( 'licence_valid', false );
			$message = $this->options->get( 'licence_valid_message', false );
			if ( ! empty( $message ) ) {
				$class = $valid ? 'notice-info' : 'notice-error';
				printf( '<div class="notice %s">%s</div>', $class, $message );
			}
		}

		public function render_setting_domain() {
			$option_name = 'domain';

			$domain = $this->options->get( $option_name, null );
			if ( empty( $domain ) ) {
				$protocol = ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] || 443 == $_SERVER['SERVER_PORT'] ) ? 'https://' : 'http://';
				$domain   = str_replace( $protocol, '', get_bloginfo( 'wpurl' ) );
			}

			WJECF_Admin_Html::render_input(
				array(
					'type'  => 'text',
					'id'    => self::DOM_PREFIX . $option_name,
					'class' => 'regular-text',
					'name'  => sprintf( '%s[%s]', self::OPTION_NAME, $option_name ),
					'value' => $domain,
				)
			);

			echo '<p class="description">'
				. __( 'The domain of your store. e.g. www.mystore.com.', 'woocommerce-jos-autocoupon' )
				. '</p>';
		}

		/**
		 * Triggered when options are being saved on the settings page
		 *
		 * @param array $input Values posted from the form
		 * @return array The options to save to the database
		 */
		public function validate_settings( $input ) {

			$options = $this->options->get();

			//Licence_key
			$licence_key_changed = false;
			if ( isset( $input['licence_key'] ) ) {
				$sanitized_licence_key = sanitize_text_field( $input['licence_key'] );
				if ( $sanitized_licence_key !== $options['licence_key'] ) {
					$licence_key_changed = true;
				}
			}

			//domain
			$domain_changed = false;
			if ( isset( $input['domain'] ) ) {
				$sanitized_domain = sanitize_text_field( $input['domain'] );
				if ( $sanitized_domain !== $options['domain'] ) {
					$domain_changed = true;
				}
			}

			//Validate or update licence
			$update_licence = $licence_key_changed || $domain_changed || ! $options['licence_valid'];
			if ( $update_licence ) {
				//Deactivate?
				if ( $options['licence_valid'] && ( $licence_key_changed || $domain_changed ) ) {
					$this->try_deactivate_licence( $options['licence_key'], $options['domain'] );
				}

				if ( $licence_key_changed ) {
					$options['licence_key'] = $sanitized_licence_key;
				}
				if ( $domain_changed ) {
					$options['domain'] = $sanitized_domain;
				}

				//Check licence; automatic activate
				$check   = $this->validate_licence( $options['licence_key'], $options['domain'] );
				$options = array_merge( $options, $check );
			}

			return $options;
		}

		private $plugin;
		private $slug;
		private $licence_key;
		private $domain = null;

		public function init_update_api() {

			if ( ! WJECF()->is_pro() ) {
				return;
			}

			$this->init_update_api_variables();

			$this->licence_key = $this->options->get( 'licence_key' );
			$this->domain      = $this->options->get( 'domain' );

			// add plugin upgrade notification
			add_action( 'after_plugin_row_' . WJECF()->plugin_file(), array( $this, 'notice_invalid_licence_key' ), 10, 2 );

			if ( empty( $this->licence_key ) || empty( $this->domain ) ) {
				return;
			}

			// Take over the update check
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'action_pre_set_site_transient_update_plugins' ) );
			// Take over the Plugin info screen
			add_filter( 'plugins_api', array( $this, 'plugins_api_call' ), 10, 3 );
		}

		private function init_update_api_variables() {
			$this->plugin = WJECF()->plugin_file();
			$this->slug = substr( $this->plugin, 0, strpos( $this->plugin, '/' ) );
		}

		public function notice_invalid_licence_key() {
			$valid = $this->options->get( 'licence_valid', false );
			if ( $valid ) {
				return;
			}

			$url           = admin_url( 'options-general.php?page=wjecf_settings' );
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			$colspan       = intval( $wp_list_table->get_column_count() );

			$message = $this->options->get( 'licence_valid_message', __( 'Licence key is not valid.', 'woocommerce-jos-autocoupon' ) );
			$message = $message . ' ' . sprintf(
				// translators: 1:url
				__( 'You will not receive automatic updates. Please enter a valid licence key <a href="%s">here</a>.', 'woocommerce-jos-autocoupon' ),
				esc_attr( $url )
			);

			?>
			<tr class="plugin-update-tr">
				<td colspan="<?php echo $colspan; ?>">
					<div class="notice inline notice-error notice-alt">
					<p><?php echo $message; ?></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Tries deactivating the licence for the given domain. Any errors will be logged.
		 * @param string $licence_key The licence key
		 * @param string $domain The domain to deactivate
		 * @return bool True if succesful
		 */
		private function try_deactivate_licence( $licence_key, $domain ) {
			try {
				$response = $this->send_request(
					'deactivate', array(
						'licence_key' => $licence_key,
						'domain'      => $domain,
					)
				);
				if ( 'success' === $response->status ) {
					// translators: 1: domain name
					$msg = sprintf( __( 'Licence for domain %s succesfully deactivated.', 'woocommerce-jos-autocoupon' ), $domain );
					$this->log( 'info', $msg );
					return true;
				}

				$msg = __( 'Failed deactivating licence. ', 'woocommerce-jos-autocoupon' );
				$this->log( 'error', $msg );
			} catch ( Exception $ex ) {
				// translators: 1: The response from the server
				$msg = sprintf( __( 'Failed deactivating licence. Invalid response from server: %s', 'woocommerce-jos-autocoupon' ), $ex->getMessage() );
				$this->log( 'error', $msg );
			}

			return false;
		}

		/**
		 * Check validity of the licence key. If it's not activated try to activate it.
		 * Returns an array with the validity and a message
		 *
		 * @return array [ 'licence_valid' => bool, 'licence_valid_message' => string ]
		 */
		public function validate_licence( $licence_key = null, $domain = null ) {

			if ( is_null( $licence_key ) ) {
				$licence_key = $this->licence_key;
			}
			if ( is_null( $domain ) ) {
				$domain = $this->domain;
			}

			if ( empty( $licence_key ) ) {
				return $this->licence_validity( false, __( 'No licence key entered.', 'woocommerce-jos-autocoupon' ) );
			}
			if ( empty( $domain ) ) {
				return $this->licence_validity( false, __( 'No valid domain entered.', 'woocommerce-jos-autocoupon' ) );
			}

			try {
				$response = $this->send_request(
					'status-check', array(
						'licence_key' => $licence_key,
						'domain'      => $domain,
					)
				);
			} catch ( Exception $ex ) {
				return $this->licence_validity(
					false,
					// translators: 1: The response from the server
					sprintf( __( 'Could not validate the licence. Invalid response from server: %s', 'woocommerce-jos-autocoupon' ), $ex->getMessage() )
				);
			}

			$response_status_code = isset( $response->status_code ) ? $response->status_code : '';

			//Activate the licence
			if ( in_array( $response_status_code, $this->statuses_must_activate ) ) {
				try {
					$response = $this->send_request(
						'activate', array(
							'licence_key' => $licence_key,
							'domain'      => $domain,
						)
					);
				} catch ( Exception $ex ) {
					return $this->licence_validity(
						false,
						// translators: 1: The response from the server
						sprintf( __( 'Could not activate the licence. Invalid response from server: %s', 'woocommerce-jos-autocoupon' ), $ex->getMessage() )
					);
				}
				$response_status_code = isset( $response->status_code ) ? $response->status_code : '';
			}

			//Get validity from known status codes
			$validity = $this->get_licence_validity( $response_status_code );
			if ( is_array( $validity ) ) {
				return $validity;
			}

			//Fallback
			$valid = 'success' === $response->status;
			if ( $valid ) {
				$message = __( 'Licence key is valid.', 'woocommerce-jos-autocoupon' );
			} elseif ( isset( $response->message ) ) {
				//Display message from server
				$message = trim( $response->message );
				if ( substr( $message, -1 ) != '.' ) {
					$message .= '.'; //Terminate sentence with a period.
				}
			} else {
				$message = __( 'Licence key is not valid.', 'woocommerce-jos-autocoupon' );
			}
			return $this->licence_validity( $valid, $message );
		}

		/**
		 * Return sanitized array [ 'licence_valid' => bool, 'licence_valid_message' => string ]
		 * @param bool $valid True if valid. false if invalid
		 * @param string $message The string
		 * @return array
		 */
		private function licence_validity( $valid, $message ) {
			return array(
				'licence_valid'         => true === $valid,
				'licence_valid_message' => sanitize_text_field( $message ),
			);
		}

		/**
		 * Try to validate the licence validity by means of the status code.
		 *
		 * @param type $status_code
		 * @return null|array null if validity could not be decided. Otherwise an array [ 'licence_valid' => bool, 'licence_valid_message' => string ]
		 */
		private function get_licence_validity( $status_code ) {
			switch ( $status_code ) {
				case 'e002':
					return $this->licence_validity( false, __( 'Invalid licence key.', 'woocommerce-jos-autocoupon' ) );

				case 'e301':
					return $this->licence_validity( false, __( 'Licence Key does not match this product.', 'woocommerce-jos-autocoupon' ) );

				case 'e312':
					return $this->licence_validity( false, __( 'Licence is not active.', 'woocommerce-jos-autocoupon' ) );

				case 's203': //Licence Key Is Unassigned
					return $this->licence_validity( false, __( 'Licence key is unassigned.', 'woocommerce-jos-autocoupon' ) );

				case 'e204': //(s204 changed to e204 in recent versions of the license manager)
					return $this->licence_validity( false, __( 'Licence key not active for current domain.', 'woocommerce-jos-autocoupon' ) );

				case 's100': //Licence Key Successfully activated for domain
				case 's205': //Licence key Is Active and Valid for Domain
					return $this->licence_validity( true, __( 'Licence key is valid.', 'woocommerce-jos-autocoupon' ) );

				case 's79001':
					return $this->licence_validity( true, __( 'Licence managed at Envato.', 'woocommerce-jos-autocoupon' ) );

				default:
					return null;
			}
		}

		/**
		 * Check for plugin update;
		 * @param object $checked_data
		 * @return object
		 */
		public function action_pre_set_site_transient_update_plugins( $checked_data ) {

			try {
				$response_block = $this->send_request( 'plugin_update' );
				if ( is_null( $response_block ) ) {
					return $checked_data;
				}
			} catch ( Exception $ex ) {
				$this->log( 'error', $ex->getMessage() );
				return $checked_data;
			}

			//retrieve the last message within the $response_block

			$response_status_code = isset( $response_block->status_code ) ? $response_block->status_code : '';
			$validity             = $this->get_licence_validity( $response_status_code );

			if ( is_array( $validity ) ) {
				if ( $validity['licence_valid'] !== $this->options->get( 'licence_valid' ) ) {
					$this->options->set( 'licence_valid', $validity['licence_valid'] );
					$this->options->set( 'licence_valid_message', $validity['licence_valid_message'] );
					$this->options->save();
				}

				if ( ! $validity['licence_valid'] ) {
					$this->log( 'warning', $validity['licence_valid_message'] );
					return $checked_data;
				}
			}

			//UPDATE AVAILABLE!
			$message = isset( $response_block->message ) ? $response_block->message : '';
			if ( is_object( $message ) && ! empty( $message ) ) { // Feed the update data into WP updater
				//NOTE: $message is a stdClass object

				//include slug and plugin data
				$message->slug   = $this->slug;
				$message->plugin = $this->plugin;

				$checked_data->response[ $this->plugin ] = $message;
			}

			return $checked_data;
		}


		public function plugins_api_call( $def, $action, $args ) {
			if ( ! is_object( $args ) || ! isset( $args->slug ) || $args->slug != $this->slug ) {
				return $def;
			}

			try {
				$response_block = $this->send_request( $action );
			} catch ( Exception $ex ) {
				$this->log( 'error', $ex->getMessage() );
				return new WP_Error(
					'plugins_api_failed',
					__( 'An Unexpected HTTP Error occurred during the API request.', 'woocommerce-jos-autocoupon' )
				);
			}
			$response = $response_block->message;

			if ( ! is_object( $response ) || empty( $response ) ) {
				return false;
			}

			//include slug and plugin data
			$response->slug   = $this->slug;
			$response->plugin = $this->plugin;

			$response->sections = (array) $response->sections;
			$response->banners  = (array) $response->banners;

			return $response;
		}

		private function send_request( $action, $args = array() ) {
			$request_array = $this->prepare_request( $action, $args );
			$request_uri   = self::AUTO_UPDATE_API_URL . '?' . http_build_query( $request_array, '', '&' );
			$data          = wp_remote_get( $request_uri );

			if ( is_wp_error( $data ) ) {
				throw new Exception( $data->get_error_message() );
			}

			$response_code = isset( $data['response']['code'] ) ? $data['response']['code'] : 'null';
			if ( 200 != $response_code ) {
				throw new Exception( sprintf( 'Invalid response %s from server', $response_code ) );
			}

			$response_block = json_decode( $data['body'] );

			if ( ! is_array( $response_block ) ) {
				$this->log( 'debug', sprintf( 'Invalid response block from %s: %s', $request_uri, print_r( $data, true ) ) );
				throw new Exception( 'Invalid response block from server' );
			}

			if ( count( $response_block ) == 0 ) {
				return null;
			}

			$response_block = end( $response_block );  //Last item

			if ( ! is_object( $response_block ) || empty( $response_block ) ) {
				throw new Exception( 'Empty response from server' );
			}

			return $response_block;
		}

		/**
		 *  Build an array with request parameters
		 * @param string $action
		 * @param array $args paramters to overwrite or append
		 * @return array
		 */
		private function prepare_request( $action, $args = array() ) {
			global $wp_version;

			$request = array(
				'woo_sl_action'     => $action,
				'version'           => WJECF()->plugin_version(),
				'product_unique_id' => self::AUTO_UPDATE_PRODUCT_ID,
				'licence_key'       => $this->licence_key,
				'domain'            => $this->domain,
				'wp-version'        => $wp_version,
			);
			return array_merge( $request, $args );
		}
	}
}
