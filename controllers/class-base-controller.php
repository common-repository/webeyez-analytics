<?php
/**
 * Webeyez: Base_Controller class
 *
 * Class made for simple common functions that every other class needs
 *
 * @package Webeyez\Controllers
 */

namespace Webeyez\Controllers;

/**
 * Class Base_Controller
 *
 * @package Webeyez\Controllers
 */
class Base_Controller {

	/**
	 * Constructor of the class
	 */
	public function __construct() {
	}

	/**
	 * Generating Id for cookie
	 *
	 * @return string
	 */
	public function generate_uid() {
		return 'wz_id_' . wp_generate_password( 8, false );
	}

	/**
	 * Send cookies names summary to header in case of AJAX request
	 */
	public function cookies_header() {
		$cookies_summary = $this->get_session();

		if ( ! $cookies_summary ) {
			return;
		}

		header( 'Access-Control-Expose-Headers:wz-cookie-name' );
		header( 'wz-cookie-name:' . $cookies_summary );
	}

	/**
	 * Send cookies names summary to cookie in case of notAJAX request
	 */
	public function wz_name_cookie_set() {
		$cookies_summary = $this->get_session();

		setcookie( 'wz-cookie-name', $cookies_summary, time() + 60, '/' );
	}

	/**
	 * Sets unique key for transient
	 *
	 * @return bool|string
	 */
	protected function transient_name() {
		$unique_key = substr( md5( isset( $_SERVER['REMOTE_ADDR'] ) ), 0, 8 );
		$transient_name = 'wz-cookie-name' . $unique_key;

		return $transient_name;
	}

	/**
	 * Sets cookie names in session
	 *
	 * @param string $uid Cookie identifier.
	 */
	public function set_names_session( $uid ) {
		$transient_value = get_transient( $this->transient_name() );

		if ( $transient_value ) {
			$transient_value[] = $uid;
		} else {
			$transient_value = [];
			$transient_value[] = $uid;
		}

		set_transient( $this->transient_name(), $transient_value, 60 );
	}

	/**
	 * Gets cookie names from session
	 *
	 * @return string
	 */
	public function get_session() {
		$transient_name = $this->transient_name();
		$transient_value = get_transient( $transient_name );

		if ( $transient_value ) {
			$cookies_name_summary = implode( ', ', $transient_value );

			delete_transient( $transient_name );
			return $cookies_name_summary;
		} else {
			return false;
		}
	}

	/**
	 * Sets error to args array
	 *
	 * @param string $error_message Error message.
	 * @param string $uid Cookie identifier.
	 * @param array  $args Data that goes to cookie.
	 */
	public function set_woocommerce_error( $error_message, $uid, $args ) {
		$args['error']          = true;
		$args['data']['errors'] = $error_message;

		$this->add_to_cookie( $uid, $args );
	}

	/**
	 * Adding and encoding info to cookie
	 *
	 * @param string $uid Cookie identifier.
	 * @param array  $args Data that goes to cookie.
	 * @param int    $time Cookie expiration time.
	 *
	 * @return bool
	 */
	public function add_to_cookie( $uid, $args = [], $time = 60 ) {
		$this->set_names_session( $uid );

		return setcookie( $uid, json_encode( $args ), time() + $time, '/' );
	}

	/**
	 * Checks if there is cookie with webeyez key and sets it if there isn`t
	 *
	 * @return mixed|string
	 */
	public function get_key_cookie() {
		if ( isset( $_COOKIE['webeyez_key'] ) && ! empty( $_COOKIE['webeyez_key'] ) ) {
			$webeyez_key = sanitize_text_field( wp_unslash( $_COOKIE['webeyez_key'] ) );
		} else {
			$webeyez_user = get_option( 'webeyez_user' );
			$webeyez_key = $webeyez_user['orgClientKey'];
			setcookie( 'webeyez_key', $webeyez_key, time() + 86400, '/' );
		}
		return $webeyez_key;
	}

	/**
	 * Setting error function
	 *
	 * @param object $errors WP_Error class object.
	 */
	public function error_set( $errors ) {
		if ( ! empty( $errors->get_error_codes() ) ) {
			$args['data']['errors'] = $errors->get_error_codes();
			$args['error']          = true;
		}
	}

}
