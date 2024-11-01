<?php
/**
 * Webeyez: Cart class
 *
 * Class made for cart page actions tracking
 *
 * @package Webeyez\Controllers
 */

namespace Webeyez\Controllers;

use Webeyez\Controllers\Helpers\Helper_Cart;

/**
 * Class Cart
 *
 * @package Webeyez\Controllers
 */
class Cart extends Base_Controller {

	/**
	 * Helper_Cart object
	 *
	 * @var object Helper_Cart Helper class for service functions.
	 */
	public $helper;

	/**
	 * Cart constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->helper = new Helper_Cart();

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_check' ], 10, 3 );

		add_action( 'woocommerce_remove_cart_item', [ $this, 'remove_from_cart_check' ], 10, 2 );

		add_filter( 'woocommerce_update_cart_validation', [ $this, 'update_cart_check' ], 10, 4 );
		add_filter( 'woocommerce_update_cart_action_cart_updated', [ $this, 'update_cart_error' ], 10 );

		add_action( 'template_redirect', [ $this, 'view_cart_check' ], 99 );
		add_action( 'woocommerce_cart_has_errors', [ $this, 'cart_view_error' ], 10 );

	}

	/**
	 * Add to cart click detection
	 *
	 * @param bool $true Boolean whether item was added to cart.
	 * @param int  $product_id Item id.
	 * @param int  $quantity Item quantity.
	 *
	 * @return mixed
	 */
	public function add_to_cart_check( $true, $product_id, $quantity ) {
		$uid                      = $this->generate_uid();
		$product                  = wc_get_product( $product_id );
		$args                     = [];
		$args['event']            = 'add_cart_item';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['data']['name']     = $product->get_title();
		$args['data']['price']    = get_post_meta( $product_id, '_price', true );
		$args['data']['sku']      = get_post_meta( $product_id, '_sku', true );
		$args['data']['currency'] = get_woocommerce_currency();
		$args['data']['quantity'] = $quantity;
		$args['data']['total']    = $args['data']['price'] * $args['data']['quantity'];
		$args['error']            = false;

		if ( post_password_required( $product_id ) ) {
			$args['error']['type'] = 'error';
			$args['error']['text'] = 'Protected product';

			$this->add_to_cookie( $uid, $args );
		} else {
			$this->add_to_cookie( $uid, $args );
		}

		if ( ! wp_doing_ajax() ) {
			$this->wz_name_cookie_set();
		}

		return $true;
	}

	/**
	 * Remove from cart detection
	 *
	 * @param string $cart_item_key Key of the item in cart.
	 * @param object $this_item Cart class object.
	 */
	public function remove_from_cart_check( $cart_item_key, $this_item ) {
		$uid                      = $this->generate_uid();
		$line_item                = $this_item->removed_cart_contents[ $cart_item_key ];
		$product                  = wc_get_product( $line_item['product_id'] );
		$args                     = [];
		$args['event']            = 'remove_cart_item';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['data']['name']     = $product->get_name();
		$args['data']['price']    = get_post_meta( $line_item['product_id'], '_price', true );
		$args['data']['sku']      = get_post_meta( $line_item['product_id'], '_sku', true );
		$args['data']['currency'] = get_woocommerce_currency();
		$args['data']['quantity'] = $line_item['quantity'];
		$args['data']['total']    = $line_item['line_total'];
		$args['error']            = false;

		if ( isset( $_GET['remove_item'] ) && ! empty( $_GET['remove_item'] ) ) {
			$this->add_to_cookie( $uid, $args );
		} else {
			$this->add_to_cookie( $uid, $args );
		}
	}

	/**
	 * Cart page view detection
	 */
	public function view_cart_check() {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'cart_view';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );

		if ( is_cart() ) {
			$cart                     = WC()->cart->get_cart();
			$order_items              = [];
			$args['data']['products'] = $this->helper->cart_foreach( $cart, $order_items );
			$args['data']['total']    = wc_prices_include_tax() ? WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() : WC()->cart->get_cart_contents_total();
			$args['data']['currency'] = get_woocommerce_currency();
			$args['error']            = false;

			$this->add_to_cookie( $uid, $args );
			$this->wz_name_cookie_set();
		}
	}

	/**
	 * Cart error tracking
	 */
	public function cart_view_error() {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'cart_view';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['error']['type']    = 'error';
		$args['error']['text']    = 'There are some issues with the items in your cart. Please go back to the cart page and resolve these issues before checking out';

		$this->add_to_cookie( $uid, $args );
		$this->wz_name_cookie_set();
	}

	/**
	 * Update cart detection
	 *
	 * @param bool   $true Boolean checks whether cart was successfully updated.
	 * @param string $cart_item_key Key of the item in cart.
	 * @param array  $values Item data.
	 * @param int    $quantity Item quantity.
	 *
	 * @return mixed
	 */
	public function update_cart_check( $true, $cart_item_key, $values, $quantity ) {
		$uid                      = $this->generate_uid();
		$args                     = [];
		$args['event']            = 'update_cart';
		$args['data']             = [];
		$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
		$args['data']['name']     = $values['data']->get_name();
		$args['data']['price']    = $values['line_total'];
		$args['data']['sku']      = $values['data']->get_sku();
		$args['data']['currency'] = get_woocommerce_currency();
		$args['data']['quantity'] = $quantity;
		$args['error']            = false;

		$this->add_to_cookie( $uid, $args );

		return $true;
	}

	/**
	 * Check if there was error on cart update
	 *
	 * @param bool $cart_updated Boolean checks whether cart was successfully updated.
	 *
	 * @return bool
	 */
	public function update_cart_error( $cart_updated ) {
		if ( false == $cart_updated ) {
			// Clears our session as there are another cookies ready to fire that we don`t need.
			$this->get_session();

			$uid                      = $this->generate_uid();
			$args                     = [];
			$args['event']            = 'update_cart';
			$args['data']             = [];
			$args['data']['datetime'] = date( 'Y-m-d H:i:s' );
			$args['error']['type']    = 'error';
			$args['error']['text']    = 'Update cart item error';

			$this->add_to_cookie( $uid, $args );
		}

		return $cart_updated;
	}
}
