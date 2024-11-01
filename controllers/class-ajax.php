<?php
/**
 * Webeyez: Ajax class
 *
 * Class made for AJAX actions tracking
 *
 * @package Webeyez\Controllers
 */

namespace Webeyez\Controllers;

/**
 * Class Ajax
 *
 * @package Webeyez\Controllers
 */
class Ajax extends Base_Controller {

	/**
	 * Ajax constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'wp_doing_ajax', [ $this, 'wp_doing_ajax' ] );
	}

	/**
	 * Function wp_doing_ajax
	 *
	 * This function is used to send request headers on any ajax action if we prepared cookie values.
	 *
	 * @param bool $doing_ajax Whether the current request is a WordPress Ajax request.
	 *
	 * @return boolean
	 */
	public function wp_doing_ajax( $doing_ajax ) {
		if ( $doing_ajax ) {
			$this->cookies_header();
		}

		return $doing_ajax;
	}

}
