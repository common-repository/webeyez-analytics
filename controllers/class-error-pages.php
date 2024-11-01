<?php
/**
 * Webeyez: Error class
 *
 * Class made for error pages tracking
 *
 * @package Webeyez\Controllers
 */

namespace Webeyez\Controllers;

/**
 * Class Error_Pages
 *
 * @package Webeyez\Controllers
 */
class Error_Pages extends Base_Controller {

	/**
	 * Error_Pages constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'wp_die_handler', [ $this, 'page_500' ], 10 );
		add_action( 'template_redirect', array( $this, 'page_404' ) );

	}

	/**
	 * Function for cart_view 404 page error tracking
	 *
	 * @param string $page_id Page id from database.
	 *
	 * @return bool|int
	 */
	public function url_404_check( $page_id ) {
		if ( ! is_page() ) {
			return false;
		}
		$page_url = explode( '/', rtrim( get_permalink( $page_id ), '/\\' ) );
		$url = isset( $_SERVER['SCRIPT_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_URI'] ) ) : null;
		$result = strpos( $url, end( $page_url ) );

		return $result;
	}

	/**
	 * Page 404 error including cases with
	 */
	public function page_404() {
		$cart_page_id     = get_option( 'woocommerce_cart_page_id' );
		$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );

		if ( false !== $this->url_404_check( $cart_page_id ) && is_404() ) {
			$uid                   = $this->generate_uid();
			$args                  = [];
			$args['event']         = 'cart_view';
			$args['data']          = [];
			$args['data']['date']  = date( 'Y-m-d H:i:s ' );
			$args['error']['type'] = 'error';
			$args['error']['text'] = 'Cart page failed to load';

			$this->add_to_cookie( $uid, $args );

			$this->wz_name_cookie_set();

			add_action( 'wp_head', [ $this, 'webeyez_404_error_script' ], 0 );

		} elseif ( false !== $this->url_404_check( $checkout_page_id ) && is_404() ) {
			$uid                   = $this->generate_uid();
			$args                  = [];
			$args['event']         = 'checkout_view';
			$args['data']          = [];
			$args['data']['date']  = date( 'Y-m-d H:i:s ' );
			$args['error']['type'] = 'error';
			$args['error']['text'] = 'Checkout page failed to load';

			$this->add_to_cookie( $uid, $args );
			$this->wz_name_cookie_set();

			add_action( 'wp_head', [ $this, 'webeyez_404_error_script' ], 0 );
		} elseif ( is_404() ) {
			add_action( 'wp_head', [ $this, 'webeyez_404_error_script' ], 0 );
		}
	}

	/**
	 * Inserting webeyez required globals in case of 404 error code page
	 */
	public function webeyez_404_error_script() {
		?>
		<script>
			window.wz_status_code = 404;
		</script>
		<?php
	}

	/**
	 * Page 500 error in case of wp_die trigger
	 *
	 * @param string $var wp_die type that goes into handler.
	 *
	 * @return array
	 */
	public function page_500( $var ) {
		switch ( $var ) {
			case '_default_wp_die_handler':
				return [ $this, '_webeyez__wp_die_handler' ];
			case '_xml_wp_die_handler':
				return [ $this, '_webeyez__wp_die_handler' ];
			case '_xmlrpc_wp_die_handler':
				return [ $this, '_webeyez__wp_die_handler' ];
			case '_jsonp_wp_die_handler':
				return [ $this, '_webeyez__wp_die_handler' ];
			case '_json_wp_die_handler':
				return [ $this, '_webeyez__wp_die_handler' ];
			case '_ajax_wp_die_handler':
				return [ $this, '_webeyez__wp_die_handler' ];
		}

	}

	/**
	 * Page 500 error handler
	 *
	 * @param string $message Message with added webeyez script.
	 * @param string $title Error page title.
	 * @param array  $error_args Arguments to control behavior.
	 */
	public function _webeyez__wp_die_handler( $message, $title, $error_args ) {
		$new_message = $this->webeyez_page_500_script_add( $message );
		_default_wp_die_handler( $new_message, $title, $error_args );
	}

	/**
	 * Inserting webeyez required globals in case of 500 error code page
	 *
	 * @param string $message Original error message.
	 *
	 * @return string
	 */
	public function webeyez_page_500_script_add( $message ) {
		ob_start();
		include plugin_dir_path( __DIR__ ) . 'templates/script.php';
		if ( is_wp_error( $message ) ) {
			$new_message = $message->get_error_message() . ob_get_clean();
			return $new_message;
		}
		$new_message = $message . ob_get_clean();

		return $new_message;
	}

}
