<?php

class Affiliate_WP_Gravity_forms_Register {

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'gform_submit_button', array( $this, 'hidden_affwp_action' ), 10, 2 );
		add_action( 'affwp_insert_affiliate', array( $this, 'add_affiliate_meta' ), 10, 2 );
		add_action( 'affwp_set_affiliate_status', array( $this, 'delete_affiliate_meta' ), 100, 3 );
		add_action( 'gform_after_submission', array( $this, 'update_gform_meta' ) );
	}

	/**
	 * Adds a hidden affwp_action input field to prevent an extra email being sent
	 * when "Auto Register New Users" is enabled
	 *
	 * @since 1.0.9.2
	 */
	function hidden_affwp_action( $button, $form ) {
		$hidden_field = '<input type="hidden" name="affwp_action" value="affiliate_register" />';
		return $hidden_field . $button;
	}

	/**
	 * Register the affiliate / user
	 *
	 * @since 1.0
	 */
	public function register_user( $entry, $form ) {

		$email = isset( $entry[ affwp_afgf_get_field_id( 'email' ) ] ) ? $entry[ affwp_afgf_get_field_id( 'email' ) ] : '';

		// email is always required for logged out users
		if ( ! is_user_logged_in() && ! $email ) {
			return;
		}

		$password         = affwp_afgf_get_field_value( $entry, 'password' );
		$username         = affwp_afgf_get_field_value( $entry, 'username' );
		$payment_email    = affwp_afgf_get_field_value( $entry, 'payment_email' );
		$promotion_method = affwp_afgf_get_field_value( $entry, 'promotion_method' );
		$website_url      = affwp_afgf_get_field_value( $entry, 'website' );

		if ( ! $username ) {
			$username = $email;
		}

		$name_ids    = affwp_afgf_get_name_field_ids();
		$first_name  = '';
		$last_name   = '';

		if ( $name_ids ) {

			// dual first name/last name field
			$name_ids = array_filter ( affwp_afgf_get_name_field_ids() );

			if ( count( $name_ids ) > 2 ) {

				// extended
				$first_name = isset( $entry[ (string) $name_ids[1] ] ) ? $entry[ (string) $name_ids[1] ] : '';
				$last_name  = isset( $entry[ (string) $name_ids[3] ] ) ? $entry[ (string) $name_ids[3] ] : '';

			} else if ( count( $name_ids ) == 2 ) {

				// normal
				$first_name = isset( $entry[ (string) $name_ids[0] ] ) ? $entry[ (string) $name_ids[0] ] : '';
				$last_name  = isset( $entry[ (string) $name_ids[1] ] ) ? $entry[ (string) $name_ids[1] ] : '';

			} else {

				// simple
				$first_name = isset( $entry[ affwp_afgf_get_field_id( 'name' ) ] ) ? $entry[ affwp_afgf_get_field_id( 'name' ) ] : '';

			}

		}

		// AffiliateWP will show the user as "user deleted" unless a display name is given
		if ( $first_name ) {

			if ( $last_name ) {
				$display_name = $first_name . ' ' . $last_name;
			} else {
				$display_name = $first_name;
			}

		} else {
			$display_name = $username;
		}

		$status = affiliate_wp()->settings->get( 'require_approval' ) ? 'pending' : 'active';

		if ( ! is_user_logged_in() ) {

			// use password fields if present, otherwise randomly generate one
			$password = $password ? $password : wp_generate_password( 12, false );

			$args = apply_filters( 'affiliatewp_afgf_insert_user', array(
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => $password,
				'display_name' => $display_name,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'entry_id'     => $entry['id']
			), $username, $email, $password, $display_name, $first_name, $last_name, $entry['id'] );

			$user_id = wp_insert_user( $args );

		} else {

			$user_id                  = get_current_user_id();
			$user                     = (array) get_userdata( $user_id );
			$args                     = (array) $user['data'];
			$args['has_user_account'] = true;
			$args['entry_id']         = $entry['id'];

		}

		if ( $promotion_method ) {
			update_user_meta( $user_id, 'affwp_promotion_method', $promotion_method );
		}

		if ( $website_url ) {
			wp_update_user( array( 'ID' => $user_id, 'user_url' => $website_url ) );
		}

		// add affiliate
		$affiliate_id = affwp_add_affiliate( array(
			'status'        => $status,
			'user_id'       => $user_id,
			'payment_email' => $payment_email
		) );

		if ( ! is_user_logged_in() ) {

			// Prevent OptimizeMember from killing the final registration process
			add_filter( 'ws_plugin__optimizemember_login_redirect', '__return_false' );
			$this->log_user_in( $user_id, $username );

		}

		// Retrieve affiliate ID. Resolves issues with caching on some hosts, such as GoDaddy
		$affiliate_id = affwp_get_affiliate_id( $user_id );

		// store entry ID in affiliate meta so we can retrieve it later
		affwp_update_affiliate_meta( $affiliate_id, 'gravity_forms_entry_id', $entry['id'] );

		do_action( 'affwp_register_user', $affiliate_id, $status, $args );

	}

	/**
	 * Adds a "gravity_forms_password_reset" flag to the affiliate meta if the
	 * affiliate needs a password reset link emailed to them.
	 *
	 * @since 1.0.16
	 */
	public function add_affiliate_meta( $affiliate_id, $args ) {

		// Get the form ID of the affiliate registration form.
		$registration_form_id = affwp_afgf_get_registration_form_id();

		// Get the form object.
		$form = GFAPI::get_form( $registration_form_id );

		// Set an initial flag as to whether the affiliate needs a password reset link.
		$password_reset_needed = false;

		if ( $form ) {
			$password_field_id = '';
			foreach( $form['fields'] as $field ) {
				// Find the password field, if present.
				if ( 'password' === $field->type ) {
					// Store the field ID from the form.
					$password_field_id = $field['id'];
					break;
				}
			}
		}

		// A password field exists on the form.
		if ( $password_field_id ) {
			// Construct the key so we can get the value of password from $_POST.
			$field_id = 'input_' . $password_field_id;

			/*
			 * If the password field is empty, the affiliate chose not to enter
			 * a custom password. A password reset link is needed.
			 */
			if ( empty( $_POST[$field_id] ) ) {
				$password_reset_needed = true;
			}
		} else {
			// No password field exists on form, password reset link is needed.
			$password_reset_needed = true;
		}

		$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
		$current_user = get_current_user_id();

		/*
		 * Add the affiliate meta if the affiliate needs a password reset link,
		 * but skip it if the affiliate user is already logged in.
		 */
		if ( true === $password_reset_needed && $user_id !== $current_user ) {
			affwp_update_affiliate_meta( $affiliate_id, 'gravity_forms_password_reset', true );
		}

	}

	/**
	 * Deletes the "gravity_forms_password_reset" flag in the affiliate meta
	 * once the affiliate has been approved.
	 *
	 * @since 1.0.16
	 */
	function delete_affiliate_meta( $affiliate_id = 0, $status = '', $old_status = '' ) {

		if ( empty( $affiliate_id ) || 'active' !== $status ) {
			return;
		}

		if ( ! in_array( $old_status, array( 'active', 'pending' ), true )
			&& ! did_action( 'affwp_affiliate_register' )
		) {
			return;
		}

		if ( doing_action( 'affwp_add_affiliate' ) && empty( $_POST['welcome_email'] ) ) {
			return;
		}

		// Delete affiliate meta now that the affiliate has been approved.
		affwp_delete_affiliate_meta( $affiliate_id, 'gravity_forms_password_reset' );

	}

	/**
	 * Log the user in
	 *
	 * @since 1.0
	 */
	private function log_user_in( $user_id = 0, $user_login = '', $remember = false ) {

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return;
		}

		wp_set_current_user( $user_id, $user_login );
		wp_set_auth_cookie( $user_id, $remember );

		do_action( 'wp_login', $user_login, $user );

	}

	/**
	 * Updates the affiliate registration entry in Gravity Forms for logged in users.
	 *
	 * The Username and Email Address fields are purposely disabled on the front-end
	 * affiliate registration form if the user is already logged in. Because of this,
	 * the field's values are not stored with the Gravity Form entry.
	 *
	 * This updates the entry meta when the affiliate registration is submitted
	 * so the Username and Email Address of the current user is stored along with the entry.
	 *
	 * @since 1.0.18
	 *
	 * @param array $entry The entry data.
	 */
	public function update_gform_meta( $entry ) {

		// Return early if the user isn't logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// The form ID.
		$form_id = $entry['form_id'];

		$registration_form_id = affwp_afgf_get_registration_form_id();

		// Bail if this is not the registration form.
		if ( $form_id !== $registration_form_id ) {
			return;
		}

		// Get the current user.
		$current_user = wp_get_current_user();

		// Bail if the current user does not exist.
		if ( ! $current_user->exists() ) {
			return;
		}

		// The current user's username.
		$user_name = $current_user->user_login;

		// The current user's email.
		$email = $current_user->user_email;

		// The entry ID to update.
		$entry_id = $entry['id'];

		// The ID of the username field update.
		$field_id_username = affwp_afgf_get_field_id( 'username' );

		// The ID of the email field update.
		$field_id_email = affwp_afgf_get_field_id( 'email' );

		// Update the username.
		gform_update_meta( $entry_id, $field_id_username, $user_name, $form_id );

		// Update the email.
		gform_update_meta( $entry_id, $field_id_email, $email, $form_id );

	}

}
