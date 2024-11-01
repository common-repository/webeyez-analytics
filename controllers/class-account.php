<?php
/**
 * Webeyez: Account class
 *
 * Class made for account actions tracking
 *
 * @package Webeyez\Controllers
 */

namespace Webeyez\Controllers;

/**
 * Class Account
 *
 * @package Webeyez\Controllers
 */
class Account extends Base_Controller {

	/**
	 * Account constructor.
	 */
	public function __construct() {

		parent::__construct();
		// Fires when submitting registration form data, before the user is created.
		add_action( 'wp_login', [ $this, 'user_login_check' ], 10, 2 );
		add_filter( 'login_errors', [ $this, 'login_errors' ], 10 );

		add_action( 'wp_logout', [ $this, 'user_logout_check' ] );

		add_filter( 'send_password_change_email', [ $this, 'user_password_change_check' ], 10, 3 );
		add_action( 'validate_password_reset', [ $this, 'user_password_lost_error' ], 0 );
		add_action( 'woocommerce_save_account_details_errors', [ $this, 'user_password_change_error' ], 10, 2 );

		add_action( 'register_post', [ $this, 'user_register_check' ], 10, 3 );
		add_action( 'woocommerce_register_post', array( $this, 'user_register_check' ), 10, 3 );

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

	}

	/**
	 * Detect any login error
	 *
	 * @param string $errors Error message with html.
	 *
	 * @return string
	 */
	public function login_errors( $errors ) {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'customer_login';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['error']['type']    = 'error';
		$args['error']['text']    = ! empty( $errors ) ? $errors : 'Login error';

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();

		return $errors;
	}

	/**
	 * Check user login
	 *
	 * @param string $user_login User login.
	 * @param object $user WP_User class object.
	 */
	public function user_login_check( $user_login, $user ) {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'customer_login';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['error']            = false;

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * User registration detection
	 *
	 * @param string $sanitized_user_login Sanitized user login.
	 * @param string $user_email User email.
	 * @param object $errors WP_Error class object.
	 */
	public function user_register_check( $sanitized_user_login, $user_email, $errors ) {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'customer_register';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['error']            = false;
		if ( ! empty( $errors->get_error_codes() ) ) {
			$args['error']['type'] = 'error';
			$args['error']['text'] = $errors->get_error_codes();
		}

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * User logout detection
	 */
	public function user_logout_check() {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'customer_logout';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['error']            = false;

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * Checks if there any errors on password loss
	 */
	public function user_password_lost_error() {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'customer_change_password';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['error']['type']    = 'error';
		$args['error']['text']    = 'Password lost error';

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * Checks if there any errors on password change
	 *
	 * @param object $errors WP_Error class object.
	 * @param object $user WP_User class object.
	 */
	public function user_password_change_error( &$errors, &$user ) {
		if ( ! empty( wc_get_notices( 'error' ) ) ) {
			$uid                      = $this->generate_uid();
			$args                     = [];
			$args['event']            = 'customer_change_password';
			$args['data']             = [];
			$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
			$args['error']['type']    = 'error';
			$args['error']['text']    = 'Password change error';

			$this->add_to_cookie( $uid, $args );
			$this->wz_name_cookie_set();
		}
	}

	/**
	 * Password change detection
	 *
	 * @param bool  $send Whether to send the email.
	 * @param array $user The original user array.
	 * @param array $userdata The updated user array.
	 *
	 * @return bool
	 */
	public function user_password_change_check( $send, $user, $userdata ) {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'customer_change_password';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['data']['user_id']  = $userdata['ID'];
		$args['error']            = false;

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();

		return $send;
	}

}
