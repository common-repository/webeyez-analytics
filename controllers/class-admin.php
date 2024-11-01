<?php
/**
 * Webeyez: Admin class
 *
 * Class made for action on admin page
 *
 * @package Webeyez
 */

namespace Webeyez\Controllers;

/**
 * Class Admin
 *
 * @package Webeyez
 */
class Admin {

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		// Make settings page in admin.
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );

		add_filter( 'plugin_action_links_' . WEBEYEZ_FILE, [ $this, 'plugin_actions' ], 10, 1 );
	}

	/**
	 * Init in admin panel
	 */
	public function admin_init() {
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_links' ], 10, 2 );
	}

	/**
	 * Settings button
	 *
	 * @param array $links Default links array.
	 *
	 * @return mixed
	 */
	public function plugin_actions( $links ) {
		$links['settings'] = '<a href="' . menu_page_url( 'webeyez', false ) . '">Settings</a>';
		return $links;
	}

	/**
	 * Call to API on activation
	 */
	protected function api_call_on_activation() {
		$wp_http = new \WP_Http();
		$domain = home_url();
		$domain = str_replace( [ 'http://', 'https://' ], '', $domain );
		$wp_http->request( 'https://sec.webeyez.com/plugindownloads/wp/' . $domain, [ 'GET' ] );
	}

	/**
	 * Link to Webeyez web page
	 *
	 * @param array  $links Array of links.
	 * @param string $file File name.
	 *
	 * @return array
	 */
	public function add_plugin_links( $links, $file ) {
		$plugin_name = plugin_basename( dirname( __FILE__ ) . '/webeyez.php' );
		if ( $plugin_name == $file ) {
			$links[] = '<a href="https://www.webeyez.com/" target="_blank">' . esc_html__( 'View details', 'webeyez' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Plugin activation
	 */
	public function activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$this->api_call_on_activation();
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivation() {
	}

	/**
	 * Plugin uninstall
	 */
	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		} else {
			delete_option( 'webeyez_user' );
		}
	}

	/**
	 * Print settings page
	 */
	public function render_settings_page() {
		require_once( WEBEYEZ_DIR . 'admin/settings-page.php' );
	}

	/**
	 * Add settings page in admin
	 */
	public function admin_menu() {
		add_options_page(
			__( 'Webeyez', 'webeyez' ),
			__( 'Webeyez', 'webeyez' ),
			'manage_options',
			'webeyez',
			[ $this, 'render_settings_page' ]
		);
	}

}
