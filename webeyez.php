<?php
/**
 * Webeyez
 *
 * @package Webeyez
 * @author Webeyez*
 *
 * Plugin Name: Webeyez
 * Description: Webeyez Plugin
 * Version: 1.9.1
 * Author: Webeyez
 * Text Domain: webeyez
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'controllers/class-admin.php';
require_once 'class-init.php';

require_once 'controllers/class-base-controller.php';
require_once 'controllers/class-account.php';
require_once 'controllers/helpers/class-helper-cart.php';
require_once 'controllers/class-cart.php';
require_once 'controllers/helpers/class-helper-checkout.php';
require_once 'controllers/class-checkout.php';
require_once 'controllers/class-error-pages.php';
require_once 'controllers/class-ajax.php';

define( 'WEBEYEZ_VERSION', '1.9.1' );
define( 'WEBEYEZ_DIR', plugin_dir_path( __FILE__ ) );
define( 'WEBEYEZ_URL', plugin_dir_url( __FILE__ ) );
define( 'WEBEYEZ_FILE', plugin_basename( __FILE__ ) );

$admin_page = new \Webeyez\Controllers\Admin();

register_activation_hook( __FILE__, [ $admin_page, 'activation' ] );
register_deactivation_hook( __FILE__, [ '\Webeyez\Controllers\Admin', 'deactivation' ] );
register_uninstall_hook( __FILE__, [ '\Webeyez\Controllers\Admin', 'uninstall' ] );

new \Webeyez\Init();

new \Webeyez\Controllers\Account();
new \Webeyez\Controllers\Cart();
new \Webeyez\Controllers\Checkout();
new \Webeyez\Controllers\Error_Pages();
new \Webeyez\Controllers\Ajax();
