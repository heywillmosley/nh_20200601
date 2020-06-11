<?php

namespace Barn2\WPT_Lib\Plugin\License\Admin;

use Barn2\WPT_Lib\Registerable,
    Barn2\WPT_Lib\Plugin\License\License;

/**
 * Handles the display and saving of the license key on the plugin settings page.
 *
 * @package   Barn2/barn2-lib
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class License_Key_Setting implements Registerable, License_Setting {

    const OVERRIDE_HASH  = 'caf9da518b5d4b46c2ef1f9d7cba50ad';
    const ACTIVATE_KEY   = 'activate_key';
    const DEACTIVATE_KEY = 'deactivate_key';
    const CHECK_KEY      = 'check_key';

    private $license;
    private $is_woocommerce;
    private $saving_key = false;

    public function __construct( License $license, $is_woocommerce ) {
        $this->license        = $license;
        $this->is_woocommerce = (bool) $is_woocommerce;
    }

    public function register() {
        \add_action( 'admin_init', array( $this, 'process_license_action' ), 5 );
    }

    /**
     * Process a license action from the plugin license settings page (i.e. activate, deactivate or check license)
     */
    public function process_license_action() {
        if ( $this->is_license_action( self::ACTIVATE_KEY ) ) {
            $license_setting = \filter_input( \INPUT_POST, $this->get_license_setting_name(), \FILTER_SANITIZE_STRING, \FILTER_REQUIRE_ARRAY );

            if ( isset( $license_setting['license'] ) ) {
                $activated = $this->activate_license( $license_setting['license'] );

                $this->add_settings_message(
                    __( 'License key successfully activated.', 'woocommerce-product-table' ),
                    __( 'There was an error activating your license key.', 'woocommerce-product-table' ),
                    $activated
                );
            }
        } elseif ( $this->is_license_action( self::DEACTIVATE_KEY ) ) {
            $deactivated = $this->license->deactivate();

            $this->add_settings_message(
                __( 'License key successfully deactivated.', 'woocommerce-product-table' ),
                __( 'There was an error deactivating your license key, please try again.', 'woocommerce-product-table' ),
                $deactivated
            );
        } elseif ( $this->is_license_action( self::CHECK_KEY ) ) {
            $this->license->refresh();

            $this->add_settings_message(
                __( 'The license key looks good!', 'woocommerce-product-table' ),
                __( 'There\'s a problem with your license key.', 'woocommerce-product-table' ),
                $this->license->is_active()
            );
        }
    }

    public function get_license_key_setting() {
        $setting = array(
            'title' => __( 'License key', 'woocommerce-product-table' ),
            'type'  => 'text',
            'id'    => $this->get_license_setting_name() . '[license]',
            'desc'  => $this->get_license_description(),
            'class' => 'regular-text'
        );

        if ( $this->is_woocommerce ) {
            $setting['desc_tip'] = __( 'The licence key is contained in your order confirmation email.', 'woocommerce-product-table' );
        }

        if ( $this->is_license_setting_readonly() ) {
            $setting['custom_attributes'] = array(
                'readonly' => 'readonly'
            );
        }

        return $setting;
    }

    public function get_license_override_setting() {
        $override = \filter_input( \INPUT_GET, 'license_override', \FILTER_SANITIZE_STRING );

        return $override ? array(
            'type'    => 'hidden',
            'id'      => 'license_override',
            'default' => $override
            ) : array();
    }

    /**
     * Save the specified license key.
     *
     * If there is a valid key currently active, the current key will be deactivated first
     * before activating the new one.
     *
     * @param string $license_key The license key to save.
     * @return string The license key.
     */
    public function save_license_key( $license_key ) {
        if ( $this->saving_key ) {
            return $license_key;
        }

        if ( array_intersect( array( self::DEACTIVATE_KEY, self::ACTIVATE_KEY, self::CHECK_KEY ), array_keys( $_POST ) ) ) {
            return $license_key;
        }

        $this->saving_key = true;
        $license_key      = \filter_var( $license_key, \FILTER_SANITIZE_STRING );

        // Deactivate old license key first if it was valid.
        if ( $this->license->is_active() && $license_key !== $this->license->get_license_key() ) {
            $this->license->deactivate();
        }

        // If new license key is different to current key, or current key isn't active, attempt to activate.
        if ( $license_key !== $this->license->get_license_key() || ! $this->license->is_active() ) {
            $this->activate_license( $license_key );
        }

        $this->saving_key = false;
        return $license_key;
    }

    public function save_posted_license_key() {
        if ( $this->saving_key ) {
            return;
        }

        $license_setting = filter_input( \INPUT_POST, $this->get_license_setting_name(), \FILTER_DEFAULT, \FILTER_REQUIRE_ARRAY );

        if ( ! isset( $license_setting['license'] ) ) {
            return;
        }

        $this->save_license_key( $license_setting['license'] );
    }

    public function get_license_setting_name() {
        return $this->license->get_setting_name();
    }

    private function activate_license( $license_key ) {
        // Check if we're overriding the license activation.
        $override = \filter_input( \INPUT_POST, 'license_override', \FILTER_SANITIZE_STRING );

        if ( $override && $license_key && self::OVERRIDE_HASH === md5( $override ) ) {
            $this->license->override( $license_key, 'active' );
            return true;
        }

        return $this->license->activate( $license_key );
    }

    private function add_settings_message( $sucess_message, $error_message, $success = true ) {
        if ( $this->is_woocommerce ) {
            if ( $success ) {
                \WC_Admin_Settings::add_message( $sucess_message );
            } else {
                \WC_Admin_Settings::add_error( $error_message );
            }
        } else {
            $message = $success ? $sucess_message : $error_message;
            $type    = $success ? 'updated' : 'error';
            \add_settings_error( $this->get_license_setting_name(), 'license_message', $message, $type );
        }
    }

    /**
     * Retrieve the description for the license key input, to display on the settings page.
     *
     * @return string The license key status message
     */
    private function get_license_description() {
        $buttons = array(
            'check'      => $this->license_action_button( self::CHECK_KEY, 'Check' ), //@todo: Spinner icon
            'activate'   => $this->license_action_button( self::ACTIVATE_KEY, __( 'Activate', 'woocommerce-product-table' ) ),
            'deactivate' => $this->license_action_button( self::DEACTIVATE_KEY, __( 'Deactivate', 'woocommerce-product-table' ) )
        );

        $message = $this->license->get_status_help_text();

        if ( $this->license->is_active() ) {
            $message = \sprintf( '<span style="color:green;">✓&nbsp;%s</span>', $message );
        } elseif ( $this->license->get_license_key() ) {
            // If we have a license key and it's not active, mark it red for user to take action.
            if ( $this->license->is_inactive() && $this->is_license_action( 'deactivate_key' ) ) {
                // ...except if the user has just deactivated, in which case just show a plain confirmation message.
                $message = __( 'License key deactivated.', 'woocommerce-product-table' );
            } else {
                $message = \sprintf( '<span style="color:red;">%s</span>', $message );
            }
        }

        if ( $this->is_license_setting_readonly() ) {
            unset( $buttons['activate'] );
        } else {
            unset( $buttons['check'], $buttons['deactivate'] );
        }

        return \implode( '', $buttons ) . ' ' . $message;
    }

    private function is_license_action( $action ) {
        return
            isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] &&
            ( $this->get_license_setting_name() === \filter_input( INPUT_POST, $action, FILTER_SANITIZE_STRING ) );
    }

    private function is_license_setting_readonly() {
        return $this->license->is_active();
    }

    private function license_action_button( $input_name, $button_text ) {
        return \sprintf(
            '<button type="submit" class="button" name="%1$s" value="%2$s" style="margin-right:4px;">%3$s</button>',
            \esc_attr( $input_name ),
            \esc_attr( $this->get_license_setting_name() ),
            $button_text
        );
    }

}
