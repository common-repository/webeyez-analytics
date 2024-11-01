<?php
/**
 * Webeyez: Checkout class
 *
 * Class made for checkout page actions tracking
 *
 * @package Webeyez\Controllers
 */

namespace Webeyez\Controllers;

use Webeyez\Controllers\Helpers\Helper_Checkout;

/**
 * Class Checkout
 *
 * @package Webeyez\Controllers
 */
class Checkout extends Base_Controller {

	/**
	 * Helper_Checkout object
	 *
	 * @var object Helper_Checkout Helper class for service functions.
	 */
	public $helper;
	/**
	 * Checkout constructor.
	 */
	public function __construct() {

		parent::__construct();

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		$this->helper = new Helper_Checkout();

		add_action( 'woocommerce_order_status_completed', [ $this, 'payment_complete_check' ], 99, 1 );
		add_action( 'woocommerce_order_status_failed', [ $this, 'payment_complete_error' ], 99, 1 );

		add_action( 'woocommerce_checkout_order_processed', [ $this, 'order_complete_check' ], 99, 3 );

		add_action( 'woocommerce_checkout_create_order', [ $this, 'add_shipping_details_check' ], 10, 1 );

		add_action( 'woocommerce_checkout_create_order', [ $this, 'select_payment_method_check' ], 10, 1 );
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'payment_method_error_check' ], 20, 2 );

		add_action( 'woocommerce_after_checkout_form', [ $this, 'checkout_page_check' ], 20, 1 );
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'checkout_errors_check' ], 30, 2 );

		add_action( 'woocommerce_after_checkout_validation', [ $this, 'shipping_details_error_check' ], 10, 2 );

	}

	/**
	 * Chosen shipping details detection
	 *
	 * @param object $order Order class object.
	 */
	public function add_shipping_details_check( $order ) {
		$uid                             = $this->generate_uid();
		$args                            = [];
		$args['event']                   = 'add_shipping_details';
		$args['data']                    = [];
		$args['data']['datetime']        = date( 'Y-m-d H:i:s' );
		$args['data']['shipping_method'] = $order->get_shipping_method();
		$args['error']                   = false;

		$this->add_to_cookie( $uid, $args );
	}

	/**
	 * Error tracking for shipping method detection
	 *
	 * @param array  $data Data array with order details.
	 * @param object $errors WP_Error class object.
	 */
	public function shipping_details_error_check( $data, $errors ) {
		$this->helper->after_checkout_validation_errors( 'add_shipping_details', 'shipping_method', $data, $errors );
	}

	/**
	 * Payment method detection
	 *
	 * @param object $order Order class object.
	 *
	 * @return mixed
	 */
	public function select_payment_method_check( $order ) {
		$uid                            = $this->generate_uid();
		$args                           = [];
		$args['event']                  = 'select_payment_method';
		$args['data']                   = [];
		$args['data']['datetime']       = date( 'Y-m-d H:i:s' );
		$args['data']['payment_method'] = $order->get_payment_method();
		$args['error']                  = false;

		$this->add_to_cookie( $uid, $args );
	}

	/**
	 * Error tracking for payment method detection
	 *
	 * @param array  $data Data array with order details.
	 * @param object $errors WP_Error class object.
	 */
	public function payment_method_error_check( $data, $errors ) {
		$this->helper->after_checkout_validation_errors( 'select_payment_method', 'payment_method', $data, $errors );
	}

	/**
	 *  Checkout page detection
	 */
	public function checkout_page_check() {
		$uid                      = $this->generate_uid();
		$cart                     = WC()->cart->get_cart();
		$args                     = [];
		$order_items              = [];
		$args['event']            = 'checkout_view';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['data']['products'] = $this->helper->checkout_foreach( $args, $cart, $order_items );
		$args['data']['total']    = wc_prices_include_tax() ? WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() : WC()->cart->get_cart_contents_total();
		$args['data']['currency'] = get_woocommerce_currency();
		$args['error']            = false;

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * Payment errors detection
	 *
	 * @param int $order_id Order id.
	 */
	public function payment_complete_error( $order_id ) {
		$order                          = wc_get_order( $order_id );
		$uid                            = $this->generate_uid();
		$args                           = [];
		$args['event']                  = 'place_payment';
		$args['data']                   = [];
		$args['data']['datetime']       = date( 'Y-m-d H:i:s' );
		$args['data']['payment_method'] = $order->get_payment_method();
		$args['error']['type']          = 'error';
		$args['error']['text']          = 'Payment failed';

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * Payment complete detection
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function payment_complete_check( $order_id ) {
		$order                          = wc_get_order( $order_id );
		$uid                            = $this->generate_uid();
		$args                           = [];
		$args['event']                  = 'place_payment';
		$args['data']                   = [];
		$args['data']['datetime']       = date( 'Y-m-d H:i:s' );
		$args['data']['payment_method'] = $order->get_payment_method();
		$args['data']['total']          = $order->get_total();
		$args['data']['currency']       = get_woocommerce_currency();
		$args['error']                  = false;

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * Order complete detection
	 *
	 * @param int    $order_id Order id.
	 * @param array  $data Data array with order details.
	 * @param object $order Order class object.
	 */
	public function order_complete_check( $order_id, $data, $order ) {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$order_items              = [];
		$args['event']            = 'place_order';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['data']['products'] = $this->helper->order_foreach( $order, $order_items );
		$args['data']['total']    = $order->get_total() - $order->get_shipping_total();
		$args['data']['currency'] = get_woocommerce_currency();
		$args['error']            = false;

		$this->add_to_cookie( $uid, $args );
	}

	/**
	 * Error tracking on "Place order" button pressed
	 *
	 * @param array  $data Data array with order details.
	 * @param object $errors WP_Error class object.
	 */
	public function checkout_errors_check( $data, $errors ) {
		if ( ! empty( $errors->get_error_codes() ) ) {
			$uid                      = $this->generate_uid();
			$args                     = [];
			$args['event']            = 'place_order';
			$args['data']             = [];
			$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
			$args['error']['type']    = 'error';
			$args['error']['text']    = $errors->get_error_codes();

			$this->add_to_cookie( $uid, $args );
		}
	}

}
