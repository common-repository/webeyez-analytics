<?php
/**
 * Webeyez: Cart helper class
 *
 * Class Helper made for cart page actions service functions
 *
 * @package Webeyez\Controllers\Helpers
 */

namespace Webeyez\Controllers\Helpers;

use Webeyez\Controllers\Base_Controller;

/**
 * Class Helper_Cart
 *
 * @package Webeyez\Controllers\Helpers
 */
class Helper_Cart extends Base_Controller {

	/**
	 * Loop function getting items properties on cart page
	 *
	 * @param array $cart_array Array that contains items from cart.
	 * @param array $array_to_push Array that returns with values.
	 *
	 * @return mixed
	 */
	public function cart_foreach( $cart_array, $array_to_push ) {
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
		}

		return $array_to_push;

	}

}
