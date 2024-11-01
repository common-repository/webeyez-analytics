<script>
	window.wz_framework = 2;
	window.wz_status_code = 500;
</script>

<?php
/**
 * Webeyez: Script page
 *
 * Script page for Webeyez plugin.
 *
 * @package Webeyez
 */

$webeyez_key = new \Webeyez\Controllers\Base_Controller();
$webeyez_key = $webeyez_key->get_key_cookie();

if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
	define( 'AUTOSAVE_INTERVAL', 0 );
}

add_action( 'wp_enqueue_scripts', 'wz_enqueue' );

/**
 * Enqueuing wzbody script
 *
 * @param string $webeyez_key Webeyez user key.
 */
function wz_enqueue( $webeyez_key ) {
	wp_enqueue_script( 'wzbody', '//sec.webeyez.com/js/' . $webeyez_key . '/wzbody.js', [], WEBEYEZ_VERSION, true );
	wp_print_scripts( 'wzbody' );
}

do_action( 'wp_enqueue_scripts', $webeyez_key );
