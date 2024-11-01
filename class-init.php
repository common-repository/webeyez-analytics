<?php
/**
 * Webeyez: Frontend class
 *
 * Class for frontend part of Webeyez plugin.
 *
 * @package Webeyez
 */

namespace Webeyez;

/**
 * Class Frontend
 *
 * @package Webeyez
 */
class Init {

	/**
	 * Webeyez initialization
	 */
	public function __construct() {
		// Load text domain.
		load_plugin_textdomain( 'webeyez', false, basename( dirname( __FILE__ ) ) . '/languages' );

		if ( is_admin() ) {

			// Enqueue admin styles and scripts.
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_script' ] );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

				// Update Key Ajax.
				add_action( 'wp_ajax_webeyez_key', [ $this, 'webeyez_key' ] );

			}
		} else {

			if ( $this->webeyez_user_key() ) {

				add_action( 'login_head', [ $this, 'webeyez_framework' ], 0 );
				add_action( 'wp_head', [ $this, 'webeyez_framework' ], 0 );

				// Adds script as async src.
				add_filter( 'script_loader_tag', [ $this, 'add_async_attribute' ], 10, 2 );

			}
		}
	}

	/**
	 * Global variable for Webeyez frontend
	 */
	public function webeyez_framework() {
		$wz_user = $this->webeyez_user_key();
		?>
		<script>
			window.wz_framework = 2;
		</script>
		<script type="text/javascript">
			var e=document.createElement("script");e.type="text/javascript",e.async=!0,e.src="//sec.webeyez.com/js/<?php echo esc_js( $wz_user['orgClientKey'] ); ?>/wzbody.js",document.head.appendChild(e)
		</script>
		<?php
	}

	/**
	 * User key
	 *
	 * @return bool|void
	 */
	public function webeyez_user_key() {

		$wz_user = $this->is_webeyez_implemented();

		return $wz_user;

	}

	/**
	 * Admin panel scripts
	 *
	 * @param string $hook_suffix settings_page_webeyez.
	 */
	public function admin_enqueue_script( $hook_suffix ) {

		// Enable styles and scripts on Webeyez page.
		if ( 'settings_page_webeyez' == $hook_suffix ) {
			wp_enqueue_style( 'webeyez', WEBEYEZ_URL . 'admin/css/stylesheet.css', [], WEBEYEZ_VERSION );
			wp_enqueue_script( 'webeyez', WEBEYEZ_URL . 'admin/js/scripts.js', [ 'jquery' ], WEBEYEZ_VERSION, true );
		}

	}

	/**
	 * Key check
	 */
	public function webeyez_key() {

		if ( ! check_admin_referer( 'webeyez_key', 'webeyez_key_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Incorrect request page.', 'webeyez' ) ] );
		}

		isset( $_POST['webeyez_key'] ) ? $user_key = sanitize_text_field( wp_unslash( $_POST['webeyez_key'] ) ) : null;

		if ( empty( $user_key ) ) {
			wp_send_json_error( [ 'message' => __( 'Please check all fields.', 'webeyez' ) ] );
		}

		$response = wp_remote_get( 'https://sec.webeyez.com/js/' . $user_key . '/wzhead.js' );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( [ 'message' => __( 'Webeyez server is unavailable.', 'webeyez' ) ] );
		} else {
			$head_code_response = wp_remote_retrieve_body( $response );
		}

		if ( empty( $head_code_response ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid client key.', 'webeyez' ) ] );
		} else {
			$check = strpos( $head_code_response, 'window' );
			if ( false !== $check ) {
				update_option(
					'webeyez_user',
					[
						'orgClientKey' => $user_key,
						'wzhead'       => $head_code_response,
					]
				);
				setcookie( 'webeyez_key', $user_key, time() + 86400, '/' );

				wp_send_json_success( __( 'Key updated successfully!', 'webeyez' ) );
			}
		}
	}

	/**
	 * Async load of wzbody.js
	 *
	 * @param string $tag The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 *
	 * @return mixed
	 */
	public function add_async_attribute( $tag, $handle ) {
		if ( 'wzbody' !== $handle ) {
			return $tag;
		} else {
			return str_replace( ' src', ' async src', $tag );
		}
	}

	/**
	 * Checks is client activated his plugin
	 *
	 * @return bool|void
	 */
	public function is_webeyez_implemented() {

		$webeyez_implemented = false;
		$webeyez_user        = get_option( 'webeyez_user' );

		if ( ! empty( $webeyez_user['orgClientKey'] ) && ! empty( $webeyez_user['wzhead'] ) ) {

			$webeyez_implemented = $webeyez_user;

		}

		return $webeyez_implemented;

	}

}
