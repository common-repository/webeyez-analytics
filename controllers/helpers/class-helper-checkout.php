<?php
/**
 * Webeyez: Checkout helper class
 *
 * Class Helper made for checkout page actions service functions
 *
 * @package Webeyez\Controllers\Helpers
 */

namespace Webeyez\Controllers\Helpers;

use Webeyez\Controllers\Base_Controller;

/**
 * Class Helper_Checkout
 *
 * @package Webeyez\Controllers\Helpers
 */
class Helper_Checkout extends Base_Controller {

	/**
	 * Core for shipping_method\payment_method error tracking
	 *
	 * @param string $action_name Name of the action.
	 * @param string $data_name Name of key in $data array.
	 * @param array  $data Data array with order details.
	 * @param object $errors WP_Error class object.
	 */
	public function after_checkout_validation_errors( $action_name, $data_name, $data, $errors ) {
		if ( ! empty( $errors->get_error_codes() ) ) {
			$uid                        = $this->generate_uid();
			$args                       = [];
			$args['event']              = $action_name;
			$args['data']               = [];
			$args['data']['datetime']   = date( 'Y-m-d H:i:s' );
			$args['data'][ $data_name ] = $data[ $data_name ];
			$args['data']['errors']     = $errors->get_error_codes();
			$args['error']              = true;

			$this->add_to_cookie( $uid, $args );
		}
	}

	/**
	 * Loop function getting items properties on checkout page
	 *
	 * @param array $args Arguments goes to cookie.
	 * @param array $cart_array Array that contains items from cart.
	 * @param array $array_to_push Array that returns with values.
	 *
	 * @return mixed
	 */
	public function checkout_foreach( $args, $cart_array, $array_to_push ) {
		foreach ( $cart_array as $item ) {
			array_push(
				$array_to_push,
				[
					'name'     => $item['data']->get_name(),
					'price'    => $item['line_total'],
					'sku'      => $item['data']->get_sku(),
					'currency' => get_woocommerce_currency(),
					'quantity' => $item['quantity'],
				]
			);
			$args['currency'] = get_woocommerce_currency();
		}

		return $array_to_push;

	}

	/**
	 * Loop function getting items properties on order page
	 *
	 * @param object $order Order id.
	 * @param array  $array_to_push Array that returns with values.
	 *
	 * @return mixed
	 */
	public function order_foreach( $order, $array_to_push ) {
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			array_push(
				$array_to_push,
				[
					'name'     => $product->get_name(),
					'price'    => $item->get_total(),
					'sku'      => $product->get_sku(),
					'currency' => get_woocommerce_currency(),
					'quantity' => $item->get_quantity(),
				]
			);
		}

		return $array_to_push;
	}

}
